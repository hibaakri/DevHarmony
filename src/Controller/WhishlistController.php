<?php

namespace App\Controller;

use App\Entity\Whishliste;
use App\Controller\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;




class WhishlistController extends AbstractController
{
    #[Route('/whishlist', name: 'app_whishlist')]
    public function index(): Response
    {
        return $this->render('whishlist/index.html.twig', [
            'controller_name' => 'WhishlistController',
        ]);
    }

    #[Route('/page', name: 'app_page')]
    public function page(EntityManagerInterface $entityManager):Response
    {
    $wishlists = $entityManager->getRepository(Whishliste::class)->findAll();
    dump($wishlists);

    return $this->render('whishlist/page.html.twig', [
        'whishlists' => $wishlists,
    ]);


}
}
