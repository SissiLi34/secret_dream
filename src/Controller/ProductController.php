<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/{slug}', name: 'app_product_category')]
    //Je me fais livrer avec le repository ma table catégorie de ma bdd
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        // Je demande a afficher 1 catégoriegrâce au critère findoneby
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
}