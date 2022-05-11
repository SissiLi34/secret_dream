<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductController extends AbstractController
{
    #[Route('/category/{slug}', name: 'app_product_category', priority:-1)]
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
        if (!$category) {
            throw $this->createNotFoundException("La catégorie demandée n'existe pas");
        }

        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            // Je passe la catégory au template twig
            'category' => $category
        ]);
    }


    // C'est l'URL qu' permettre d'acceder au produit
    #[Route('/{category_slug}/{slug}', name: 'product_show')]
    //Je créée la function qui permettra d'afficher un seul produit en recevant mon slug et en permettant aux données de ma bdd d'etre remonté de la table des produits grace à productRepository
    public function show($slug, ProductRepository $productRepository)
    {

        $product = $productRepository->findOneBy([
            'slug' => $slug
        ]);

        //Je lance une nouvelle exeption si le produit n'existe pas
        if (!$product) {
            throw $this->createNotFoundException("Le produit demandé n'existe pas");
        }

        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }


    #[Route('/admin/product/{id}/edit', name: 'product_edit')]
    //dans ma fonction je reçois l'identifiant qu'il y a dans ma route, j'ai besoin d'aller cherher mon id a éditer donc je fais appel au repository
    public function edit($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator)
    {

        //J'utilise la fonction find
        $product = $productRepository->find($id);

        //Création du formulaire d'édition et du forulaire que j'ai créé
        //CréateForm permet de passer un produit et le formulaire travaillera sur celi-ciu
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $product = $form->getData();
            $em->flush();


            //Redirection sur la page du produit show

            //abstraclController nous permet d'avoir le redirect sur les url 
            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }

        //J'affiche la vue de mon formulaire
        $formView = $form->createView();



        //et je return l'affichage et le formView
        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formView' => $formView
        ]);
    }


    #[Route('/admin/product/create', name: 'product_create')]
    //création du formulaire création de produit 
    public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        $product = new Product;
        //je fais appel à ma class ProductType pour les données du formulaire
        //$this->creatForm me permet de créer un form simplement.
        //$product me permet de récupérer les données
        $form = $this->createForm(ProductType::class, $product);

        //Je demande à mon formulaire de regarder la requête actuelle et de voir si des infos qui l'interresse ou pas, si c'est le cas je les extrais
        $form->handleRequest($request);

        //Est ce que mon formulaire est soumis?
        if ($form->isSubmitted() && $form->isValid()) {

            //Je slug le name de mon produit
            $product->setSlug(strtolower($slugger->slug($product->getName())));

            //Je persiste mon product pour préparer l'enregistrement         
            $em->persist($product);

            //Et mon flush envoir la requête SQL
            $em->flush($product);

            //Redirection
            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }

        //Cette class va me permettre d'afficher la vue de mon formulaire
        $formView = $form->createView();

        //dd($data);

        return $this->render('product/create.html.twig', [
            //Je passe la variable formView, spécialisée dans l'affichage (représenté par la variable php formView) à twig 
            'formView' => $formView
        ]);
    }
}