<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart/add/{id}', name: 'app_cart_add', requirements: ["id" => "\d+"])]


    //Je rends l'identifiant du produit par son id
    public function add($id, ProductRepository $productRepository, SessionInterface $session): Response
    {
        //J'explique que ce produit existe dans le panier   
        //dd($request);


        //0. Est ce que le produit existe ?
        $product = $productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas");
        }
        //1.Retrouver le panier dans la session (sous forme de tableau)


        //2.S'il n'existe pas encore, prendre un tableau vide
        $cart = $session->get('cart', []);

        //3.Voir si le produit ($id) existe déjà dans le tableau
        //4.Si c'est le cas, augmenter la quantité
        //5.Sinon ajouter le produit avec la quantité 1
        if (array_key_exists($id, $cart)) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        //6.Enregistrer le tableau mis à jour dans la session
        //Dansla session, le cart est = au tableau cart qui a été mis a jour juste au dessus (je le re stoke)
        $session->set('cart', $cart);

        //Si je veux supprimer le panier c'est:
        //$request->getSession()->remove('cart');

// /** @var FlashBag */

// $flashBag = $session->getBag('flashes');
//La methode add ajoute des msg
// $flashBag->add('success', "Tout s'est bien passé"); 
$this->addFlash('success',["title" => "Félicitation", "content" => "Le produit a bien été ajouté au panier"]);

//La méthode get lit et supprime les msg
//dump($flashBag->get('succes'));


//dd($flashBag);

        return $this->redirectToRoute('product_show', [
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug()
        ]);
    }
}