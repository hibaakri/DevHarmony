<?php

namespace App\Controller;

use App\Entity\Whishliste;
use App\Form\WhishlisteType;
use App\Repository\WhishlisteRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Security;


#[Route('/whishliste')]
final class WhishlisteController extends AbstractController
{
    #[Route(name: 'app_whishliste_index', methods: ['GET'])]
    public function index(WhishlisteRepository $whishlisteRepository): Response
    {
        $whishlistes = $whishlisteRepository->findAll();

        // return $this->render('whishliste/index.html.twig', [
        //     'whishlistes' => $whishlisteRepository->findAll(),
        // ]);

        return $this->render('whishliste/index.html.twig', [
            'whishlistes' => $whishlistes,
        ]);
    }

    #[Route('/new', name: 'app_whishliste_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,Security $security): Response
    {
        $whishliste = new Whishliste();
        // Associer l'utilisateur connecté à la wishlist
        $user = $security->getUser();
    
    // Vérifier si l'utilisateur est connecté
        if ($user) {
        $whishliste->setUser($user);  // Assurez-vous d'avoir une méthode setUser() dans votre entité Whishliste
        } else {

        // Gérer l'erreur si l'utilisateur n'est pas connecté
        $this->addFlash('error', 'You must be logged in to create a wishlist.');
        return $this->redirectToRoute('app_login');  // Redirige vers la page de connexion
    }


        $form = $this->createForm(WhishlisteType::class, $whishliste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($whishliste);
            $entityManager->flush();

            return $this->redirectToRoute('app_whishliste_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('whishliste/new.html.twig', [
            'whishliste' => $whishliste,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_whishliste_show', methods: ['GET'])]
    public function show(Whishliste $whishliste): Response
    {
        return $this->render('whishliste/show.html.twig', [
            'whishliste' => $whishliste,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_whishliste_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Whishliste $whishliste, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(WhishlisteType::class, $whishliste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_whishliste_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('whishliste/edit.html.twig', [
            'whishliste' => $whishliste,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_whishliste_delete', methods: ['POST'])]
    public function delete(Request $request, Whishliste $whishliste, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$whishliste->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($whishliste);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_whishliste_index', [], Response::HTTP_SEE_OTHER);
    }


}
