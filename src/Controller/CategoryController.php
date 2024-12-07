<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(CategoryRepository $pr): Response
    {
          //recupération de toutes la table depuis la repository 
          $categorys = $pr->findAll() ;
          if($this->isGranted("ROLE_ADMIN"))
          {
        return $this->render('category/index.html.twig', [
           //envoie vers la Vue 
           "categorys"=> $categorys
        ]);
    }
    return $this->render('category/indexClient.html.twig', [
        //envoie vers la Vue 
        "categorys"=> $categorys
     ]);
    
    }


    #[Route('/category/add', name: 'app_category_add')]
    public function new( Request $request ,EntityManagerInterface $em): Response
    {
        //new instance 
        $category =new Category;
        //formulaire
        $form=$this->createForm(CategoryType::class,$category);

        //recuperation des données via le formulaire
        //injection de request from httpFoundation
        $form->handleRequest($request);
        
        //
        if ($form->isSubmitted() && $form->isValid())
        {
            //injection de l'entity manger interface
            $em->persist($category);//requete pour ajouter un entité a la BD
            $em->flush();//execution de req

             //redirection a votre route  souhaité 
          return $this->redirectToRoute('app_category');

        }

        return $this->render('category/add.html.twig', [
            //send formuler
            "form" => $form,
        ]);
    }



    #[Route('/category/edit/{id}', name: 'app_category_edit')]
    public function edit(  int $id , CategoryRepository $pr , Request $request, EntityManagerInterface $em): Response
    {
        // Récuperation de entité a partir de LURL (ID)
        $category = $pr->find($id)  ;

        //Formulaire 
        $form = $this->createForm(CategoryType::class, $category);

        //Récupération des donnés via le formulaire
        //Injection de Request from httpFoundation 

        $form->handleRequest($request);


        if ($form->isSubmitted()  && $form->isValid()) {
            //Injection de l entity manager Interface

            $em->persist($category); // Requéte pour Ajouter un entité a la DB
            $em->flush(); // Exécution de req 


            //redirection a votre route  souhaité 
          return $this->redirectToRoute('app_category');


        }
        return $this->render('category/edit.html.twig', [
            //send a variable to a view 

            "form" => $form->createView()
        ]);

    }


    #[Route('/category/show/{id}', name: 'app_category_show')]
    public function show(  int $id , CategoryRepository $pr , Request $request, EntityManagerInterface $em): Response
    {
        // Récuperation de entité a partir de LURL (ID)
        $category = $pr->find($id)  ;
 


        return $this->render('category/show.html.twig', [
         "category" => $category
         ]);
    }



    #[Route('/category/delete/{id}', name: 'app_category_delete')]
    public function delete(  int $id , CategoryRepository $pr , EntityManagerInterface $em): Response
    {
        // Récuperation de entité a partir de LURL (ID)
        $category = $pr->find($id)  ;

         $em->remove($category) ;
        $em->flush();


         //entitymanager interface 
         //perist + flush
         //remove + flush

         return $this->redirectToRoute('app_category');
 



        
    }





}