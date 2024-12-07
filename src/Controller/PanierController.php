<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Form\PanierType;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/panier')]
final class PanierController extends AbstractController
{
    #[Route(name: 'app_panier_index', methods: ['GET'])]
    public function index(PanierRepository $panierRepository): Response
    {
        return $this->render('panier/index.html.twig', [
            'paniers' => $panierRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_panier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $panier = new Panier();
        $form = $this->createForm(PanierType::class, $panier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($panier);
            $entityManager->flush();

            $this->addFlash('success', 'Panier ajouté avec succès !');
            return $this->redirectToRoute('app_panier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('panier/new.html.twig', [
            'panier' => $panier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_panier_show', methods: ['GET'])]
    public function show(Panier $panier): Response
    {
        return $this->render('panier/show.html.twig', [
            'panier' => $panier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_panier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Panier $panier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PanierType::class, $panier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Panier modifié avec succès !');
            return $this->redirectToRoute('app_panier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('panier/edit.html.twig', [
            'panier' => $panier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_panier_delete', methods: ['POST'])]
    public function delete(Request $request, Panier $panier, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$panier->getId(), $request->request->get('_token'))) {
            $entityManager->remove($panier);
            $entityManager->flush();

            $this->addFlash('success', 'Panier supprimé avec succès !');
        }

        return $this->redirectToRoute('app_panier_index', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/add/{produitId}', name: 'app_panier_add_produit', methods: ['POST'])]
    public function addProduit(
        int $produitId,
        Request $request,
        PanierRepository $panierRepository,
        ProduitRepository $produitRepository, // Utilise ProduitRepository
        EntityManagerInterface $entityManager
    ): Response {
        // Récupère le panier de l'utilisateur connecté
        $panier = $panierRepository->findOneBy(['user' => $this->getUser()]);
        
        // Récupère le produit
        $produit = $produitRepository->find($produitId); // Utilise produit et non product
    
        // Si le panier n'existe pas, crée-le
        if (!$panier) {
            $panier = new Panier();
            $panier->setUser($this->getUser());
            $entityManager->persist($panier);
        }
    
        // Si le produit n'existe pas, lance une exception
        if (!$produit) {
            throw $this->createNotFoundException('Produit introuvable.');
        }
    
        // Ajoute le produit au panier
        $panier->addProduit($produit); // Utilise addProduit et non addProduct
        $entityManager->flush();
    
        $this->addFlash('success', 'Produit ajouté au panier !');
    
        return $this->redirectToRoute('app_panier_show', ['id' => $panier->getId()]);
    }
    
    #[Route('/remove/{produitId}', name: 'app_panier_remove_produit', methods: ['POST'])]
    public function removeProduit(
        int $produitId,
        PanierRepository $panierRepository,
        ProduitRepository $produitRepository, // Utilise ProduitRepository
        EntityManagerInterface $entityManager
    ): Response {
        // Récupère le panier et le produit
        $panier = $panierRepository->findOneBy(['user' => $this->getUser()]);
        $produit = $produitRepository->find($produitId); // Utilise produit et non product
    
        if (!$panier || !$produit) {
            $this->addFlash('error', 'Panier ou produit introuvable.');
            return $this->redirectToRoute('app_panier_index');
        }
    
        // Retire le produit du panier
        $panier->removeProduit($produit); // Utilise removeProduit et non removeProduct
        $entityManager->flush();
    
        $this->addFlash('success', 'Produit retiré du panier !');
    
        return $this->redirectToRoute('app_panier_show', ['id' => $panier->getId()]);
    }
    


}

