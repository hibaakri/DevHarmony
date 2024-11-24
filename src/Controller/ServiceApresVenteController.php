<?php

namespace App\Controller;

use App\Entity\ServiceApresVente;
use App\Form\ServiceApresVenteType;
use App\Repository\ServiceApresVenteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ServiceApresVenteController extends AbstractController
{
    #[Route('/service/apres/vente', name: 'app_service_apres_vente')]
    public function index(ServiceApresVenteRepository $sav): Response
    {
        //recuperation de toute la table de la repository 
        $service_apres_vente = $sav->findAll();

        return $this->render('service_apres_vente/index.html.twig', [
            //envoi vers le vue
            'service_apres_vente' => $service_apres_vente,
        ]);
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

        return $this->render('service_apres_vente/show.html.twig', [
            "service_apres_vente" => $service_apres_vente,
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



//    #[Route('/service/apres/vente', name: 'app_service_apres_vente')]
//     public function index(): Response
//     {
//         return $this->render('service_apres_vente/index.html.twig', [
//             'controller_name' => 'ServiceApresVenteController',
//         ]);
//     }
}
