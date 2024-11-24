<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Form\AvisType;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AvisController extends AbstractController
{
    #[Route('/avis', name: 'app_avis')]
    public function index(AvisRepository $av): Response
    {
        //recuperation de toute la table de la repository 
        $avis = $av->findAll();

        return $this->render('avis/index.html.twig', [
            //envoi vers le vue
            'avis' => $avis,
        ]);
    }



    #[Route('/avis/add', name: 'app_avis_add')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {   //new instance
        $avis= new Avis;
        //formulaire
        $form=$this->createForm(AvisType::class,$avis);
        //recuperation des donnees via le formulaire
        //injection de request from httpfoundation
        $form->handleRequest($request);

        if ($form->isSubmitted()&& $form->isValid()){
            //injection de l entity manager interface
            $em->persist($avis); //requete pour ajouter un entite a la bd
            $em->flush(); //execution du requete
            //redirection a mon route souhaite 
            return $this->redirectToRoute('app_avis');

        }
    

        return $this->render('avis/add.html.twig', [
            //envoyer un variable a un vue
            'form' => $form,
        ]);
    }






    #[Route('/avis/edit/{id}', name: 'app_avis_edit')]
    public function edit( int $id,AvisRepository $av ,Request $request, EntityManagerInterface $em): Response
    {   //recuperation de l'entite a partir de l id
        $avis= $av->find($id);
        //formulaire
        $form=$this->createForm(AvisType::class,$avis);
        //recuperation des donnees via le formulaire
        //injection de request from httpfoundation
        $form->handleRequest($request);

        if ($form->isSubmitted()&& $form->isValid()){
            //injection de l entity manager interface
            $em->persist($avis); //requete pour ajouter un entite a la bd
            $em->flush(); //execution du requete
            //redirection a mon route souhaite 
            return $this->redirectToRoute('app_avis');

        }
    

        return $this->render('avis/edit.html.twig', [
            //envoyer un variable a un vue
            'form' => $form,
        ]);
    }


    #[Route('/avis/show/{id}', name: 'app_avis_show')]
    public function show( int $id,AvisRepository $av ,Request $request, EntityManagerInterface $em): Response
    {   //recuperation de l'entite a partir de l id
        $avis= $av->find($id);
        
    

        return $this->render('avis/show.html.twig', [
            "avis" => $avis,
        ]);
    }


    #[Route('/avis/delete/{id}', name: 'app_avis_delete')]
    public function delete( int $id,AvisRepository $av ,Request $request, EntityManagerInterface $em): Response
    {   //recuperation de l'entite a partir de l id
        $avis= $av->find($id);
        $em->remove($avis);
        $em->flush();

        //entity manager interface
        //persist+flush
        //remove+flush
        return $this->redirectToRoute('app_avis');
        
    }
}
