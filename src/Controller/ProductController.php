<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
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

    #[Route('/admin/product/create', name:'product_create')]
    //création du formulaire création de produit 
    public function create(FormFactoryInterface $factory) 
    {
        $builder = $factory->createBuilder();
        //Je construis mon formulaire
        //J'importe la class que je souhaite
        $builder->add('name', TextType::class, [
            //je passe tous mes paramètre dans le tableau
            'label' => 'Nom du produit',
            'attr' => [
                //Form bootstrap 
                'placeholder' => 'Tapez le nom du produit']
        ])
            //Descrition avec textarea
            ->add('shortDescription', TextareaType::class, [
                'label' => 'Description courte',
                'attr' => [
                'placeholder' => 'Tapez une description assez courte mais parlante pour le visiteur'
                ]
            ])
            //Ajout du prix
            ->add('price', MoneyType::class, [
                'label' => 'Prix du produit',
                'attr' => [
                'placeholder' => 'Tapez le prix du produit en €'
                ]
                ])


            //Menu déroulant
                ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'placeholder' => '--Choisir une catégorie--',
                //L'entité que je veux c'est l'entité catégorie
                'class' => Category::class,
                //Et je veux afficher le name des catégories dans la fonction et ce qui va s'afficher sera en majuscule
                'choice_label' => function (Category $category) {
                    return strtoupper($category->getName());
                }
            ]);

            $form = $builder->getForm();
            //Cette class va me permettre d'afficher la vue de mon formulaire
            $formView = $form->createView();
        
        return $this->render('product/create.html.twig', [
            //Je passe la variable formView (repésenté par la variable php formView) à twig pour qu'il l'affiche
            'formView' => $formView
        ]);
    }
}