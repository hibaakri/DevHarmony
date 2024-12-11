<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Produit;
use App\Form\AvisType;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use BaconQrCode\Common\ErrorCorrectionLevel;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProduitController extends AbstractController
{
    #[Route('/produit', name: 'app_produit')]
    public function index(ProduitRepository $pr, PaginatorInterface $paginator, Request $request): Response
    {
        $productsQuery = $pr->findproducts();


        $produits = $paginator->paginate(
            $productsQuery,
            $request->query->getInt('page', 1), // Current page number, default to 1
            2 // Number of items per page
        );
        if ($this->isGranted("ROLE_ADMIN")) {
            return $this->render('produit/index.html.twig', [
                //envoie vers la Vue 
                "produits" => $produits
            ]);
        }


        $searchTerm = $request->query->get('search', ''); // Valeur par défaut est une chaîne vide
        // $products = $pr->searchByName($searchTerm);


        $trie = $request->query->get('trie');
        if ($trie == "max") {
            $produits = $paginator->paginate(
                $pr->findproductsPrixAsc(),
                $request->query->getInt('page', 1), // Current page number, default to 1
                3 // Number of items per page
            );
        }
        if ($trie == "min") {
            $produits = $paginator->paginate(
                $pr->findproductsPrixDesc(),
                $request->query->getInt('page', 1), // Current page number, default to 1
                3 // Number of items per page
            );
        }

       

        if ($searchTerm) {

            $pp = $pr->searchByName($searchTerm);

            $produits = $paginator->paginate(
                $pp,
                $request->query->getInt('page', 1), // Current page number, default to 1
                3 // Number of items per page
            );



            return $this->render('produit/indexClient.html.twig', [
                //envoie vers la Vue 
                "produits" => $produits
            ]);
        }

        return $this->render('produit/indexClient.html.twig', [
            //envoie vers la Vue 
            "produits" => $produits
        ]);
    }


    #[Route('/produit/add', name: 'app_produit_add')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        //new instance 
        $produit = new Produit;
        //formulaire
        $form = $this->createForm(ProduitType::class, $produit);

        //recuperation des données via le formulaire
        //injection de request from httpFoundation
        $form->handleRequest($request);

        //
        if ($form->isSubmitted() && $form->isValid()) {

            /////////////////////////////// //image ////////////////////////////
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('upload_directory_produit'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image.');
                    return $this->redirectToRoute('app_produit');
                }

                $produit->setImage($newFilename);
            }

            /////////////////////////////////////
            //injection de l'entity manger interface
            $currentdatetime = new DateTimeImmutable('now');

            $produit->setCreatedat($currentdatetime);


            $em->persist($produit); //requete pour ajouter un entité a la BD
            $em->flush(); //execution de req

            //redirection a votre route  souhaité 
            return $this->redirectToRoute('app_produit');
        }

        return $this->render('produit/add.html.twig', [
            //send formuler
            "form" => $form,
        ]);
    }


    #[Route('/produit/edit/{id}', name: 'app_produit_edit')]
    public function edit(int $id, ProduitRepository $pr, Request $request, EntityManagerInterface $em): Response
    {
        // Récuperation de entoté a partir de LURL (ID)
        $produit = $pr->find($id);

        //Formulaire 
        $form = $this->createForm(ProduitType::class, $produit);

        //Récupération des donnés via le formulaire
        //Injection de Request from httpFoundation 

        $form->handleRequest($request);


        if ($form->isSubmitted()  && $form->isValid()) {



            /////////////////////////////// //image ////////////////////////////
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image.');
                    return $this->redirectToRoute('app_produit');
                }

                $produit->setImage($newFilename);
            }

            /////////////////////////////////////
            //Injection de l entity manager Interface

            $em->persist($produit); // Requéte pour Ajouter un entité a la DB
            $em->flush(); // Exécution de req 


            //redirection a votre route  souhaité 
            return $this->redirectToRoute('app_produit');
        }


        return $this->render('produit/edit.html.twig', [
            //send a variable to a view 

            "form" => $form->createView()
        ]);
    }


    #[Route('/produit/show/{id}', name: 'app_produit_show')]
    public function show(int $id, ProduitRepository $pr, Request $request, EntityManagerInterface $em): Response
    {
        // Récuperation de entité a partir de LURL (ID)
        $produit = $pr->find($id);


        if ($this->isGranted("ROLE_ADMIN")) {
            return $this->render('produit/show.html.twig', [
                "produit" => $produit
            ]);
        }





        // comment section 
        $avis = new Avis;
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if the user is not logged in
            if (!$this->getUser()) {
                $this->addFlash(
                    'error',
                    'Vous devez être connecté pour soumettre un commentaire.'
                );

                return $this->redirectToRoute('app_login'); // Redirect to the login page
            }

            // Proceed with saving the comment if the user is logged in
            $avis->setUser($this->getUser());
            $avis->setProduit($produit);
            $avis->setDateCreation(new DateTime('now'));

            $em->persist($avis);
            $em->flush();

            // Redirect to avoid form resubmission
            return $this->redirectToRoute('app_produit_show', ['id' => $produit->getId()]);
        }

        //qr code
        $localIp = gethostbyname(gethostname());

        $result = Builder::create()
       ->writer(new \Endroid\QrCode\Writer\PngWriter())
       ->data('http://'.$localIp .':8000/produit/show/' . $produit->getId() )
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(ErrorCorrectionLevel::High)
        ->size(300)
        ->margin(10)
        ->build();

    // Generate a Data URI to include image data inline
         $dataUri = $result->getDataUri();





        return $this->render('produit/showClient.html.twig', [
            "produit" => $produit,
            "form" => $form->createView() ,
             'qrcode'=>$ $dataUri
        ]);
    }




    #[Route('/produit/delete/{id}', name: 'app_produit_delete')]
    public function delete(int $id, ProduitRepository $pr, EntityManagerInterface $em): Response
    {
        // Récuperation de entité a partir de LURL (ID)
        $produit = $pr->find($id);

        $em->remove($produit);
        $em->flush();


        //entitymanager interface 
        //perist + flush
        //remove + flush

        return $this->redirectToRoute('app_produit');
    }
}
