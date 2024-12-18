<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Produit;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/panier')]
final class PanierController extends AbstractController
{
    /**
     * Affiche les produits du panier avec le total.
     */
    #[Route('/', name: 'app_panier_afficher', methods: ['GET'])]
public function afficherPanier(SessionInterface $session, ProduitRepository $produitRepository): Response
{
    // Récupérer le panier depuis la session
    $panier = $session->get('panier', []);

    // Initialiser les variables
    $produits = [];
    $total = 0;

    // Construire la liste des produits avec leur quantité et calculer le total
    foreach ($panier as $id => $quantite) {
        $produit = $produitRepository->find($id);
        if ($produit) {
            $produits[] = [
                'produit' => $produit,
                'quantite' => $quantite,
                'sousTotal' => $produit->getPrix() * $quantite,
            ];
            $total += $produit->getPrix() * $quantite;
        }
    }

    // Trier les produits par prix croissant
    usort($produits, function ($a, $b) {
        return $a['produit']->getPrix() <=> $b['produit']->getPrix();
    });

    // Calcul de la remise (10% si total > 150 €)
    $remise = $total >= 150 ? $total * 0.1 : 0;

    // Calcul des frais de livraison (gratuits si total > 100 €)
    $fraisLivraison = $total >= 100 ? 0 : 7;

    // Calcul du total à payer
    $totalAPayer = $total - $remise + $fraisLivraison;

    // Messages informatifs
    $messageLivraisonGratuite = $total >= 100
        ? "Félicitations, la livraison est gratuite pour votre commande !"
        : "  La livraison est gratuite  à partir de 100 dt.";
    $messageReduction = $total >= 150
        ? "Vous bénéficiez d'une réduction de 10 %."
        : "Une réduction de 10 %  à partir  150 dt d'achat.";

    // Passer les données à la vue
    return $this->render('panier/index.html.twig', [
        'produits' => $produits,         // Liste des produits triés
        'total' => $total,               // Total des produits avant les frais et remises
        'remise' => $remise,             // Montant de la remise
        'fraisLivraison' => $fraisLivraison, // Frais de livraison
        'totalAPayer' => $totalAPayer,   // Total final à payer
        'messageLivraisonGratuite' => $messageLivraisonGratuite, // Message livraison
        'messageReduction' => $messageReduction,                 // Message réduction
    ]);
}
    

    

    /**
     * Ajoute un produit au panier.
     */
    #[Route('/ajouter/{id}', name: 'app_panier_ajouter', methods: ['POST'])]
    public function ajouterProduit(int $id, SessionInterface $session, ProduitRepository $produitRepository): Response
    {
        $produit = $produitRepository->find($id);

        if (!$produit) {
            throw $this->createNotFoundException('Produit introuvable.');
        }

        $panier = $session->get('panier', []);
        
        // Si le produit existe déjà dans le panier, augmenter la quantité
        if (isset($panier[$id])) {
            $panier[$id]++;
        } else {
            // Si c'est un nouveau produit, l'ajouter avec une quantité de 1
            $panier[$id] = 1;
        }

        $session->set('panier', $panier);

        $this->addFlash('success', 'Produit ajouté au panier !');
        return $this->redirectToRoute('app_panier_afficher');
    }

    /**
     * Modifie la quantité d'un produit dans le panier.
     */
    #[Route('/modifier/{id}', name: 'app_panier_modifier', methods: ['POST'])]
public function modifierProduit(int $id, Request $request, SessionInterface $session, ProduitRepository $produitRepository): Response
{
    $quantite = (int) $request->request->get('quantite', 1);
    $produit = $produitRepository->find($id);

    if (!$produit) {
        throw $this->createNotFoundException('Produit introuvable.');
    }

    $panier = $session->get('panier', []);
    if ($quantite > 0) {
        $panier[$id] = $quantite; // Mise à jour de la quantité
    } else {
        unset($panier[$id]); // Supprime le produit si la quantité est 0
    }

    $session->set('panier', $panier);

    $this->addFlash('success', 'Quantité mise à jour avec succès !');
    return $this->redirectToRoute('app_panier_afficher');
}


    /**
     * Supprime un produit du panier.
     */
    #[Route('/supprimer/{id}', name: 'app_panier_supprimer', methods: ['POST'])]
    public function supprimerProduit(int $id, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);

        if (isset($panier[$id])) {
            unset($panier[$id]);
        }

        $session->set('panier', $panier);

        $this->addFlash('success', 'Produit supprimé du panier !');
        return $this->redirectToRoute('app_panier_afficher');
    }




   


   







// #[Route('/panier-affichage', name: 'app_panier_affichage')]
// public function index(Panier $panier): Response
// {
//     // Appeler les méthodes du repository
//     $total = $this->panierRepository->calculerTotal($panier);
//     $totalAvecLivraison = $this->panierRepository->calculerTotalAvecLivraison($panier);

//     return $this->render('panier/index.html.twig', [
//         'panier' => $panier,
//         'total' => $total,
//         'totalAvecLivraison' => $totalAvecLivraison,
//     ]);
// }




}
