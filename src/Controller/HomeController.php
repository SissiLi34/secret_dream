<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    //Je me fais livrer le productRepository
    public function homepage(): Response
    {
        //Je créé une variable qui demandea productRepository d'aller mes chercher des produits avec des critères (3 produits) 
        // $products = $productRepository->findBy([], [], 3);
        //dd($products);
        //Je passe ma variable product à mon template
        return $this->render('home.html.twig');
    }
}