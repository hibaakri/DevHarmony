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
        
        // Calcul du total du panier
        foreach ($panier as $id => $quantite) {
            $produit = $produitRepository->find($id);
            if ($produit) {
                $produits[] = [
                    'produit' => $produit,
                    'quantite' => $quantite,
                ];
                $total += $produit->getPrix() * $quantite;
            }
        }
        
        // Calcul de la remise (10% si total > 150 €)
        $remise = 0;
        if ($total >= 150) {
            $remise = $total * 0.1;  // Remise de 10%
        }

        usort($produits, function($a, $b) {
            return $a['produit']->getPrix() <=> $b['produit']->getPrix(); // Compare les prix
        });
    
        // Calcul des frais de livraison (gratuits si total > 100 €)
        $fraisLivraison = 7;  // Frais de livraison fixes
        if ($total >= 100) {
            $fraisLivraison = 0;  // Livraison gratuite si total > 100 €
        }
    
        // Calcul du total à payer
        $totalAPayer = $total - $remise + $fraisLivraison;
    
        // Messages informatifs
        $messageLivraisonGratuite = $total >= 100 ? "Félicitations, la livraison est gratuite pour votre commande !" : "La livraison est gratuite à partir de 100 dt d'achat.";
        $messageReduction = $total >= 150 ? "Vous avez droit à une réduction de 10% !" : "Une réduction de 10% s'applique à partir de 150 dt d'achat.";
    
        // Passer les données à la vue
        return $this->render('panier/index.html.twig', [
            'produits' => $produits,  // Liste des produits à afficher dans le panier
            'total' => $total,  // Le total des produits avant les frais de livraison
            'remise' => $remise,  // La remise à appliquer
            'fraisLivraison' => $fraisLivraison,  // Les frais de livraison
            'totalAPayer' => $totalAPayer,  // Le total à payer (produits + frais de livraison)
            'messageLivraisonGratuite' => $messageLivraisonGratuite,  // Message livraison gratuite
            'messageReduction' => $messageReduction,  // Message réduction 10%
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



    #[Route('/valider-commande', name: 'valider_commande')]
    public function validerCommande(SessionInterface $session): Response
    {
        // Récupérer le panier actuel
        $panier = $session->get('panier');

        if ($panier) {
            // Finaliser la commande (panier actuel)
            $user = $this->getUser(); // Récupère l'utilisateur connecté
            $panier->setUser($user);

            // Sauvegarder le panier (ou le marquer comme validé)
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($panier);
            $entityManager->flush();
            
            // Créer un nouveau panier pour l'utilisateur
            $nouveauPanier = new Panier();
            $nouveauPanier->setUser($user); // Associe l'utilisateur au nouveau panier
            $entityManager->persist($nouveauPanier);
            $entityManager->flush();

            // Réinitialise le panier dans la session
            $session->set('panier', $nouveauPanier);
        }

        return $this->redirectToRoute('app_commande_confirmation');
    }














    // src/Controller/PanierController.php

// public function index(SessionInterface $session, ProduitRepository $produitRepository): Response
// {
//     $panier = $session->get('panier', []);
//     $panierAvecDonnees = [];
//     $total = 0;

//     // Construction du panier
//     foreach ($panier as $id => $quantite) {
//         $produit = $produitRepository->find($id);
//         if ($produit) {
//             $panierAvecDonnees[] = [
//                 'produit' => $produit,
//                 'quantite' => $quantite,
//             ];
//             $total += $produit->getPrix() * $quantite;
//         }
//     }

//     // Calcul des frais de livraison et de la remise
//     $fraisLivraison = 7; // Frais fixes par défaut
//     $remise = 0;

//     // Livraison gratuite si le total est supérieur à 100
//     if ($total > 100) {
//         $fraisLivraison = 0;
//     }

//     // Remise de 10% si le total dépasse 150
//     if ($total > 150) {
//         $remise = $total * 0.1; // 10% de remise
//     }

//     $totalAvecRemise = $total - $remise;
//     $totalAPayer = $totalAvecRemise + $fraisLivraison;

//     // Rendu de la vue avec les données nécessaires
//     return $this->render('panier/index.html.twig', [
//         'panier' => $panierAvecDonnees,
//         'total' => $total,
//         'fraisLivraison' => $fraisLivraison,
//         'remise' => $remise,
//         'totalAPayer' => $totalAPayer,
//     ]);
// }


   





//  #[Route('/panier/quantite/{id}', name:'app_panier_modifier', methods:['POST'])]
 
//  public function modifierQuantite(int $id, Request $request): JsonResponse
//  {
//      $quantite = $request->request->get('quantite');
 
//      // Mettre à jour la quantité du produit dans le panier ici
//      $panier = $this->getUser()->getPanier(); // Exemple, ajustez en fonction de votre logique
//      $produit = $panier->getProduit($id); // Trouver le produit dans le panier
//      $produit->setQuantite($quantite);
 
//      $this->getDoctrine()->getManager()->flush();
 
//      // Recalculer le total
//      $newTotal = $panier->getTotal();
 
//      return new JsonResponse([
//          'newTotal' => $newTotal,
//      ]);

// }

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
