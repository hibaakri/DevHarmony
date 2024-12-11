<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApiController extends AbstractController
{
    #[Route('/api/categories', name: 'app_api_categories')]
    public function getCategories(CategoryRepository $categoryRepository): JsonResponse
    {
        // Récupérer toutes les catégories
        $categories = $categoryRepository->findAll();
    
        // Normaliser les données
        $data = array_map(function ($category) {
            return [
                'id' => $category->getId(),
                'name' => $category->getTitre(),
            ];
        }, $categories);
    
        // Retourner les données en JSON
        return new JsonResponse($data);
    }






    #[Route('/api/products/suggestions', name: 'app_api_products')]
    public function searchProducts(HttpFoundationRequest $request, ProduitRepository $productRepository): JsonResponse
    {
         $searchTerm = $request->query->get('search', ''); // Valeur par défaut est une chaîne vide
    
        // Utiliser le repository pour effectuer la recherche dans la base de données
        $products = $productRepository->searchByName($searchTerm);
    
        // Si vous souhaitez ajouter d'autres critères de recherche (par exemple, catégorie, prix, etc.), vous pouvez les récupérer ici.
    
        // Transformer les produits en tableau pour les retourner en JSON
        $data = array_map(function ($product) {
            return [
                 'name' => $product->getTitre(),
             ];
        }, $products);
    
        // Retourner les résultats sous forme de JSON
        return new JsonResponse($data);
    }





}
