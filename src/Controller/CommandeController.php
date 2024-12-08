<?php
// src/Controller/CommandeController.php
namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Panier;
use App\Repository\CommandeRepository;
use App\Repository\PanierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    /**
     * Afficher le formulaire pour saisir l'adresse de livraison
     */
    #[Route('/commande/creer', name: 'app_commande_creer')]
    public function creerCommande(Request $request, PanierRepository $panierRepository): Response
    {
        // Récupérer les produits du panier de l'utilisateur (par exemple, basé sur l'utilisateur connecté)
        $panier = $panierRepository->findOneBy(['user' => $this->getUser()]);

        // Vérifier si le panier est vide
        if (!$panier || count($panier->getProduits()) == 0) {
            // Rediriger vers la page des produits si le panier est vide
            return $this->redirectToRoute('app_produit_index');
        }

        $commande = new Commande();

        // Créer un formulaire pour saisir l'adresse de livraison
        $form = $this->createFormBuilder($commande)
            ->add('adresseLivraison')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer l'adresse de livraison et enregistrer la commande
            $commande->setDateCommande(new \DateTime());
            $commande->setAdresseLivraison($commande->getAdresseLivraison());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($commande);
            $entityManager->flush();

            // Rediriger vers une page de confirmation de commande
            return $this->redirectToRoute('app_commande_confirmation', ['id' => $commande->getId()]);
        }

        return $this->render('commande/creer.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Page de confirmation de commande
     */
    #[Route('/commande/confirmation/{id}', name: 'app_commande_confirmation')]
    public function confirmationCommande($id, CommandeRepository $commandeRepository): Response
    {
        $commande = $commandeRepository->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        return $this->render('commande/confirmation.html.twig', [
            'commande' => $commande,
        ]);
    }
}
