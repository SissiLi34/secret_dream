<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ProductController extends AbstractController
{
    #[Route('/category/{slug}', name: 'app_product_category')]
    //Je me fais livrer avec le repository ma table catégorie de ma bdd
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        // Je demande à afficher 1 catégorie grâce au critère findoneby
        $category = $categoryRepository->findOneBy([
            // Le critère : recevoir le slug reçu dans l'URL
            'slug' => $slug
           
        ]);
        //  dd($category);
    
        //Je créée une erreur 404
        if(!$category){
        throw $this->createNotFoundException("La catégorie demandée n'existe pas"); 
    }           
 
        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            // Je passe la catégory au template twig
            'category' => $category
        ]);
    }


    // C'est l'URL qu' permettre d'acceder au produit
    #[Route('/{category_slug}/{slug}', name:'product_show')]
    //Je créée la function qui permettra d'afficher un seul produit en recevant mon slug et en permettant aux données de ma bdd d'etre remonté de la table des produits grace à productRepository
    public function show($slug, ProductRepository $productRepository) {
        
        $product = $productRepository->findOneBy([
            'slug' => $slug
        ]);

        //Je lance une nouvelle exeption si le produit n'existe pas
        if(!$product)
        {
            throw $this->createNotFoundException("Le produit demandé n'existe pas");
        }

        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }
}