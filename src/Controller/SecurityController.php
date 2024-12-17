<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository; 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


    #[Route('/forgot-password', name: 'app_forgot_password', methods: ['GET', 'POST'])]
    public function forgotPassword(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
    // Si c'est une requête POST
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');

        // Rechercher l'utilisateur par email
            $user = $userRepository->findOneByEmail($email);

            if (!$user) {
                $this->addFlash('error', 'No user found with this email.');
                return $this->redirectToRoute('app_forgot_password');
            }

            // Générer un token de réinitialisation
            $resetToken = bin2hex(random_bytes(32));// pour Génèrer un token aléatoire sécurisé.
            $user->setResetToken($resetToken);
            $entityManager->flush();

            // Envoyer un email au user
            $emailMessage = (new Email())
                ->from('rahma12lagha@gmail.com')
                ->to($user->getEmail())
                // ->to('rahma12lagha@gmail.com')
                ->subject('Password Reset Request')
                ->html('<p>Click the link to reset your password: <a href="' . 
                $this->generateUrl('app_reset_password', ['token' => $resetToken], UrlGeneratorInterface::ABSOLUTE_URL) . '">Reset Password</a></p>');
                
            // dd($emailMessage); // Pour vérifier le contenu du message

            try {
                $mailer->send($emailMessage);
                $this->addFlash('success', 'Password reset email sent.');
            } catch (\Exception $e) {
                // En cas d'erreur d'envoi, afficher un message d'erreur
                $this->addFlash('error', 'Failed to send email: ' . $e->getMessage());
                return $this->redirectToRoute('app_forgot_password');
            }

            // dd($emailMessage);

        return $this->redirectToRoute('app_login');
    }

        return $this->render('security/forgot_password.html.twig');
    }


    #[Route('/reset-password/{token}', name: 'app_reset_password', methods: ['GET', 'POST'])]
    public function resetPassword(string $token, Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
    $user = $userRepository->findOneBy(['resetToken' => $token]);

        if (!$user) {
            $this->addFlash('error', 'Invalid token.');
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('password');
            $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
            $user->setResetToken(null); // Reset the token after usage
            $entityManager->flush();

            $this->addFlash('success', 'Password has been reset successfully.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password.html.twig', ['token' => $token]);
    } 







}
