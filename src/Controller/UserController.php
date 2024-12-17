<?php

namespace App\Controller;
use App\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    
    #[Route('/user', name: 'app_users')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // ne5ou les utilisateurs 
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/utilisateurs/delete/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        // na3mel recuperation mta3 user bel id 
        $user = $entityManager->getRepository(User::class)->find($id);

        if ($user) {
            // Supprimer l'utilisateur
            $entityManager->remove($user);
            $entityManager->flush();

            // message du confirmation du suppretion 
            $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Utilisateur introuvable.');
        }

        // Rediriection vers la liste des utilisateurs
        return $this->redirectToRoute('app_users');
    }


    #[Route('/profile', name: 'app_profile')]
    #[IsGranted("ROLE_USER")] // Seuls les utilisateurs connectés peuvent accéder
    public function profile(): Response
    {
        $user = $this->getUser(); // Récupérer l'utilisateur connecté

        return $this->render('profile/profile.html.twig', [
            'user' => $user,
        ]);
        
    }

}
