<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    #[Route('/boutique', name: 'app_shop_shop')]
    public function shop(ProductRepository $productRepository): Response
    {

        //Je créé une variable qui demandea productRepository d'aller mes chercher des produits avec des critères (3 produits) 
        $products = $productRepository->findBy([], [], 3);
        //dd($products);
        
        //Je passe ma variable product à mon templat
        return $this->render('shop/shop.html.twig', [
            'products' => $products
        ]);
    }
}