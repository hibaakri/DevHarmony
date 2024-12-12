<?php

namespace App\Controller;

use App\Entity\ServiceApresVente;
use App\Form\ServiceApresVenteType;
use App\Repository\ServiceApresVenteRepository;
use BaconQrCode\Common\ErrorCorrectionLevel;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ServiceApresVenteController extends AbstractController
{
    #[Route('/service/apres/vente', name: 'app_service_apres_vente')]
    public function index(ServiceApresVenteRepository $sav ,Request $request , PaginatorInterface $paginator): Response
    {

       if ($this->getUser()) {
        # code...
       
        //recuperation de toute la table de la repository 
         if ($this->isGranted("ROLE_ADMIN")) {
            $ss = $sav->alladmin();

            $service_apres_vente = $paginator->paginate(
                $ss,
                $request->query->getInt('page', 1), // Current page number, default to 1
                3 // Number of items per page
            );

            return $this->render('service_apres_vente/index_admin.html.twig', [
                //envoi vers le vue
                'service_apres_vente' => $service_apres_vente,
            ]);
        }
        $service_apres_vente = $sav->all($this->getUser()->getId());

        $savs = $paginator->paginate(
            $service_apres_vente,
            $request->query->getInt('page', 1), // Current page number, default to 1
            1// Number of items per page
        );
        return $this->render('service_apres_vente/index.html.twig', [
            //envoi vers le vue
            'service_apres_vente' => $savs,
        ]);

    } else {
        return $this->redirectToRoute('app_login');
    }
    }



    #[Route('/service/apres/vente/add', name: 'app_service_apres_vente_add')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {   //new instance
        $service_apres_vente= new ServiceApresVente;
        //formulaire
        $form=$this->createForm(ServiceApresVenteType::class,$service_apres_vente);
        //recuperation des donnees via le formulaire
        //injection de request from httpfoundation
        $form->handleRequest($request);

        if ($form->isSubmitted()&& $form->isValid()){
            
            //image
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
                   return $this->redirectToRoute('app_service_apres_vente');
               }
   
               $service_apres_vente->setImage($newFilename);
           } 
        
           $service_apres_vente->setCreatedby($this->getUser());
            
            
            $service_apres_vente->setEtatDemande(null);
            $nowdate= new DateTime('now');
            $service_apres_vente->setDateDemande($nowdate);
            //injection de l entity manager interface
            $em->persist($service_apres_vente); //requete pour ajouter un entite a la bd
            $em->flush(); //execution du requete
            //redirection a mon route souhaite 
            return $this->redirectToRoute('app_service_apres_vente');

        }
    

        return $this->render('service_apres_vente/add.html.twig', [
            //envoyer un variable a un vue
            'form' => $form,
        ]);
    }






    #[Route('/service/apres/vente/edit/{id}', name: 'app_service_apres_vente_edit')]
    public function edit( int $id,ServiceApresVenteRepository $sav ,Request $request, EntityManagerInterface $em): Response
    {   //recuperation de l'entite a partir de l id
        $service_apres_vente= $sav->find($id);
        //formulaire
        $form=$this->createForm(ServiceApresVenteType::class,$service_apres_vente);
        //recuperation des donnees via le formulaire
        //injection de request from httpfoundation
        $form->handleRequest($request);

        if ($form->isSubmitted()&& $form->isValid()){
            //injection de l entity manager interface
            $em->persist($service_apres_vente); //requete pour ajouter un entite a la bd
            $em->flush(); //execution du requete
            //redirection a mon route souhaite 
            return $this->redirectToRoute('app_service_apres_vente');

        }
    

        return $this->render('service_apres_vente/edit.html.twig', [
            //envoyer un variable a un vue
            'form' => $form,
        ]);
    }


    #[Route('/service/apres/vente/show/{id}', name: 'app_service_apres_vente_show')]
    public function show( int $id,ServiceApresVenteRepository $sav ,Request $request, EntityManagerInterface $em): Response
    {   //recuperation de l'entite a partir de l id
        $service_apres_vente= $sav->find($id);
        if ($this->isGranted("ROLE_ADMIN")) {
            return $this->render('service_apres_vente/show_admin.html.twig', [
                "service_apres_vente" => $service_apres_vente,
            ]);
        }


$etatDemandeText = null;
if ($service_apres_vente->isEtatDemande()) {
    $etatDemandeText = 'Ok';
} elseif ($service_apres_vente->isEtatDemande() === null) {
    $etatDemandeText = 'En attente';
} else {
    $etatDemandeText = 'Refusé';
}

        $localIp = gethostbyname(gethostname());

       
$qrCode = new QrCode(
    "Type de problème : " . $service_apres_vente->getTypeProbleme() . "\n" .
    "Description : " . $service_apres_vente->getDescriptionProbleme() . "\n" .
    "ETAT : " . $etatDemandeText
 ); 
 

$writer = new PngWriter();
$dataUri = $writer->write($qrCode)->getDataUri();

    // Generate a Data URI to include image data inline
 


        return $this->render('service_apres_vente/show.html.twig', [
            "service_apres_vente" => $service_apres_vente,
            "codeqr"=>$dataUri
        ]);
    }


    #[Route('/service/apres/vente/delete/{id}', name: 'app_service_apres_vente_delete')]
    public function delete( int $id,ServiceApresVenteRepository $sav ,Request $request, EntityManagerInterface $em): Response
    {   //recuperation de l'entite a partir de l id
        $service_apres_vente= $sav->find($id);
        $em->remove($service_apres_vente);
        $em->flush();

        //entity manager interface
        //persist+flush
        //remove+flush
        return $this->redirectToRoute('app_service_apres_vente');
        
    }

    
    #[Route('/service/apres/vente/accepte/{id}', name: 'app_service_apres_vente_accepte')]
    public function accepte( int $id,ServiceApresVenteRepository $sav ,Request $request, EntityManagerInterface $em): Response
    {   //recuperation de l'entite a partir de l id
        $service_apres_vente= $sav->find($id);
        $service_apres_vente->setEtatDemande(true);

        $em->persist($service_apres_vente);
        $em->flush();

        //entity manager interface
        //persist+flush
        //remove+flush
        return $this->redirectToRoute('app_service_apres_vente');
        
    }
    
    #[Route('/service/apres/vente/refuse/{id}', name: 'app_service_apres_vente_refuse')]
    public function refuse( int $id,ServiceApresVenteRepository $sav ,Request $request, EntityManagerInterface $em): Response
    {   //recuperation de l'entite a partir de l id
        $service_apres_vente= $sav->find($id);
        $service_apres_vente->setEtatDemande(false);
        $em->persist($service_apres_vente);
        $em->flush();

        //entity manager interface
        //persist+flush
        //remove+flush
        return $this->redirectToRoute('app_service_apres_vente');
        
    }



//    #[Route('/service/apres/vente', name: 'app_service_apres_vente')]
//     public function index(): Response
//     {
//         return $this->render('service_apres_vente/index.html.twig', [
//             'controller_name' => 'ServiceApresVenteController',
//         ]);
//     }
}
