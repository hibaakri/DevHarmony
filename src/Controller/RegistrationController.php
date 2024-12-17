<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppCustomAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $plainPassword = $form->get('plainPassword')->getData();

            //  plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword)); //pour crypté le mot de passe en clair.

            $user->setResetToken(null); // Cela garantit que resetToken est `null`
            $entityManager->persist($user);
            $entityManager->flush();

            // Envoi de l'email de bienvenue
        $email = (new Email())
        ->from('rahma12lagha@gmail.com') // Adresse e-mail de votre application
        ->to($user->getEmail())       // Adresse e-mail de l'utilisateur
        ->subject('Bienvenue sur DevHarmony !')
        ->html("
            <p>Bonjour {$user->getEmail()},</p>
            <p>Merci de vous être inscrit sur notre plateforme DevHarmony !</p>
            <p>Vous pouvez dès à présent explorer toutes nos fonctionnalités.</p>
            <p>Si vous avez des questions, n'hésitez pas à nous contacter.</p>
            <p>À bientôt,</p>
            <p>L'équipe DevHarmony</p>
        ");
        $mailer->send($email);

        // Message flash de succès
        $this->addFlash('success', 'Inscription réussie , Bonjour User! Un e-mail de bienvenue vous a été envoyé.');


            return $security->login($user, AppCustomAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
