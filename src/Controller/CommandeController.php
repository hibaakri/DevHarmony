<?php
// src/Controller/CommandeController.php
namespace App\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Panier;
use App\Entity\Commande;
use App\Repository\CommandeRepository;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType; 


class CommandeController extends AbstractController
{
    private $entityManager;

    // Injecter EntityManagerInterface dans le constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Afficher le formulaire pour saisir l'adresse de livraison
     */
   
     #[Route('/commande/creer', name: 'app_commande_creer')]
     public function creerCommande(Request $request, EntityManagerInterface $entityManager): Response
     {
        // Traiter la requête du formulaire
        if ($request->isMethod('POST')) {
            // Récupérer les données envoyées via POST
            $adresse = $request->request->get('adresseLivraison');
            $codePostal = $request->request->get('codePostal');
            $gouvernement = $request->request->get('gouvernement');
            $telephone = $request->request->get('telephone');
    
            // Vérifiez que toutes les données sont valides (simple validation)
            if ($adresse && $codePostal && $gouvernement && $telephone) {
                // Créer la commande
                $commande = new Commande();

// Vérifiez si un utilisateur est connecté
              $user = $this->getUser();
               if ($user) {
                 $commande->setUser($user); // Associez l'utilisateur uniquement s'il est connecté
                 }

               $commande->setDateCommande(new \DateTime());
               $commande->setAdresseLivraison($adresse);
               $commande->setCodePostal($codePostal);
               $commande->setGouvernement($gouvernement);
               $commande->setTelephone($telephone);

// Sauvegarder la commande
             $entityManager->persist($commande);
             $entityManager->flush();
    
                // Rediriger vers la confirmation
                return $this->redirectToRoute('app_commande_confirmation', ['id' => $commande->getId()]);
            }
        }
    
        // Afficher le formulaire si aucune soumission ou données manquantes
        return $this->render('commande/creer.html.twig');
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

    /**
 * Page de paiement
 */
   #[Route('/commande/paiement', name: 'app_commande_paiement')]
    public function paiement(Request $request , EntityManagerInterface $entityManager): Response
    {
        // Création du formulaire
        $form = $this->createFormBuilder()
            ->add('paiement_comptant', CheckboxType::class, [
                'label' => 'Paiement comptant à la livraison',
                'required' => true,
                'mapped' => false, // Ne pas lier ce champ à une entité
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer la commande',
                'attr' => ['class' => 'btn btn-primary'],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $paiementComptant = $form->get('paiement_comptant')->getData();

            // Créer une nouvelle commande
            $commande = new Commande();
            $commande->setDateCommande(new \DateTime());
            $commande->setModePaiement('Comptant à la livraison');
            $commande->setDateCommande(new \DateTime());
                  $commande->setModePaiement('Comptant à la livraison');
                  $commande->setUser($this->getUser());  
                  $commande->setAdresseLivraison('');
                  $commande->setCodePostal('');
                  $commande->setGouvernement('');
                  $commande->setTelephone('');
                  $commande->setNom('');
                  $commande->setPrenom('');
                  $commande->setEmail('');
                  $commande->setIdPanier(5);
                //  $commande->setid_panier('');


            // Sauvegarder la commande en base de données
            $entityManager = $entityManager;
            $entityManager->persist($commande);
            $entityManager->flush();

            // Afficher un message de succès
            $this->addFlash('success', 'Commande enregistrée avec succès ✅.');

            // Rediriger vers une page de confirmation ou autre
            return $this->redirectToRoute('app_commande_enregistrer'); // Créez une route pour afficher le succès
        }

        return $this->render('commande/paiement.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/commande/enregistrer', name: 'app_commande_enregistrer')]
    public function enregistrerCommande(): Response
    {
    // Afficher un message de succès
    $this->addFlash('success', 'Commande enregistrée avec succès.');

    // Rendre la vue enregistrer.html.twig
    return $this->render('commande/enregistrer.html.twig');
   }


   #[Route('/commandes', name: 'app_commandes')]
    public function index(CommandeRepository $commandeRepository): Response
    {
        // Vérifiez que l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour voir vos commandes.');
            return $this->redirectToRoute('app_login');
        }

        // Récupérer les commandes de l'utilisateur
        $commandes = $commandeRepository->findCommandesByUser($user->getId());

        // Retourner une vue
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }



   
    #[Route('/login', name: 'app_login')]
    public function login(): Response
   {
    return $this->render('security/login.html.twig');
   }




//    #[Route('/commande/paiement', name: 'app_commande_paiement')]
// public function paiement(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
// {
//     // Création du formulaire
//     $form = $this->createFormBuilder()
//         ->add('paiement_comptant', CheckboxType::class, [
//             'label' => 'Paiement comptant à la livraison',
//             'required' => true,
//             'mapped' => false, // Ne pas lier ce champ à une entité
//         ])
//         ->add('submit', SubmitType::class, [
//             'label' => 'Enregistrer la commande',
//             'attr' => ['class' => 'btn btn-primary'],
//         ])
//         ->getForm();

//     $form->handleRequest($request);

//     if ($form->isSubmitted() && $form->isValid()) {
//         // Récupérer les données du formulaire
//         $paiementComptant = $form->get('paiement_comptant')->getData();

//         // Créer une nouvelle commande
//         $commande = new Commande();
//         $commande->setDateCommande(new \DateTime());
//         $commande->setModePaiement('Comptant à la livraison');
//         $commande->setUser($this->getUser()); // Assurez-vous que l'utilisateur est connecté
//         $commande->setAdresseLivraison($request->request->get('adresseLivraison'));
//         $commande->setCodePostal($request->request->get('codePostal'));
//         $commande->setGouvernement($request->request->get('gouvernement'));
//         $commande->setTelephone($request->request->get('telephone'));

//         // Sauvegarder la commande en base de données
//         $entityManager->persist($commande);
//         $entityManager->flush();

//         // Récupérer les informations de l'utilisateur
//         $user = $this->getUser();
//         $userEmail = $user->getEmail(); // Assurez-vous que l'utilisateur a une adresse email valide

//         // Récupérer les produits de la commande
//         // Supposons que vous ayez une méthode pour récupérer les produits d'une commande.
//         $produits = $commande->getProduits(); // Cette méthode dépend de votre entité Commande
//         $total = 0;
//         $produitsList = '';
//         foreach ($produits as $produit) {
//             $produitsList .= $produit->getNom() . ' - ' . $produit->getPrix() . '€<br>';
//             $total += $produit->getPrix();
//         }

//         // Créer l'email
//         $email = (new Email())
//             ->from('votre-email@domaine.com') // Remplacez par votre adresse email
//             ->to($userEmail)
//             ->subject('Votre commande est en préparation')
//             ->html(
//                 "<h2>Bonjour {$user->getPrenom()} {$user->getNom()},</h2>
//                 <p>Votre commande est en cours de préparation.</p>
//                 <p><strong>Détails de la commande :</strong></p>
//                 <p><strong>Adresse :</strong> {$commande->getAdresseLivraison()}<br>
//                 <strong>Code Postal :</strong> {$commande->getCodePostal()}<br>
//                 <strong>Téléphone :</strong> {$commande->getTelephone()}</p>
//                 <p><strong>Produits achetés :</strong><br>{$produitsList}</p>
//                 <p><strong>Total à payer :</strong> {$total}€</p>"
//             );

//         // Envoyer l'email
//         $mailer->send($email);

//         // Afficher un message de succès
//         $this->addFlash('success', 'Commande enregistrée avec succès.');

//         // Rediriger vers la page de confirmation de commande
//         return $this->redirectToRoute('app_commande_enregistrer');
//     }

//     return $this->render('commande/paiement.html.twig', [
//         'form' => $form->createView(),
//     ]);
// }
}

