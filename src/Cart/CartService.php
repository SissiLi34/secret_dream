<?php

namespace App\Cart;

use App\Cart\CartItem;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;


class CartService
{

    protected $requestStack;
    protected $productRepository;

    public function __construct(RequestStack $requestStack, ProductRepository $productRepository)
    {
        //Dans ma requestStack je vais stoker ma requestStak
        $this->requestStack = $requestStack;
        //Dans mon productRepo je vais stoker mon productRepo
        $this->productRepository = $productRepository;
    }

    //protégé puisque personne ne la renvoie, c'est un tableau
    protected function getCart(): array
    {
        //Je récupère un cart a partir de la session
        $session = $this->requestStack->getSession();
        //je demande ce que je veux lorsque ma session demande un cart ou un tableau vide
        return $session->get('cart', []);
    }



    //fonction qui permettra de dire quelle cart elle a reçu
    protected function saveCart(array $cart)
    {
        $session = $this->requestStack->getSession();
        //Je mets la session (panier) à jour
        $session->set('cart', $cart);
    }




    public function add(int $id)
    {
        // $session = $this->requestStack->getSession();
        // $session->set('$product', '$id');

        //Je demande à mon construct de recevoir la sessionInterface

        //Cette méthode doit avoir accès au panier 
        //1.Retrouver le panier dans la session (sous forme de tableau)
        //2.S'il n'existe pas encore, prendre un tableau vide
        $cart = $this->getCart();
        //3.Voir si le produit ($id) existe déjà dans le tableau
        //4.Si c'est le cas, augmenter la quantité
        //5.Sinon ajouter le produit avec la quantité 1
        if (!array_key_exists($id, $cart)) {
            $cart[$id] = 0;
        }

        $cart[$id]++;

        //6.Enregistrer le tableau mis à jour dans la session
        //Dansla session, le cart est = au tableau cart qui a été mis a jour juste au dessus (je le re stoke)
        $this->saveCart($cart);
    }



    //Permet de supprimer un produit en allant voir dans la session si le produit existe et si c'est le cas on le supprime

    public function remove(int $id)
    {
        //Je reprends un cart à partir de la getCart/session
        $cart = $this->getCart();
        //Je supprime la donnée qui est dans le [] cart de l'id récupéré
        unset($cart[$id]);
        //Je mets à jour ma session avec le produit supprimé
        $this->saveCart($cart);
    }





    public function decrement(int $id)
    {
        //Je reprends un cart à partir de la getCart/session
        $cart = $this->getCart();
        //Si une ligne avec cet id n'existe pas il n'y a rien à faire
        if (!array_key_exists($id, $cart)) {
            return;
        }

        //Soit le produit est à 1 et il faut simplement le supprimer
        if ($cart[$id] === 1) {
            //suprime le produit en appelant la function remove
            $this->remove($id); //terminé
            return;
        }

        //Sinon, si le produit est + de 1 alors il faut le décrémenter
        $cart[$id]--;
        //jz sauvegarde le nouveau panier MAJ
        $this->saveCart($cart);
    }




    //function qui retourne un entier
    public function getTotal(): int
    {
        $total = 0;
        //Pour chaque cart je prends la clé et id de la session
        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);
            //s'il n'y a pas de produit (parce qu'il n'existe plus en bdd), o ne fait pas le total la boucle doit continuer et recommencer la boucle
            if (!$product) {
                continue;
            }
            //j'incrémente le total a chaque produit rajouté
            $total += $product->getPrice() * $qty;
        }
        //résultat
        return $total;
    }





    //function qui affiche les détails du panier dans un tableau
    public function getDetailedCartItems(): array
    {
        //la boucle va passer sur chacun des produits et va trouver toutes les données de celui-ci de la bdd
        //Je stoke tout dans un tableau vide
        $detailedCart = [];

        //Pour chaque cart je prends la clé et id de la session
        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);


            //s'il n'y a pas de produit (parce qu'il n'existe plus en bdd), on ne fait pas le total la boucle doit continuer et recommencer la boucle
            if (!$product) {
                continue;
            }
//et à chaque fois je lui passe le carItm qui contient les calculs
            $detailedCart[] = new CartItem($product, $qty);
        }
        //c'est une suite de cartItem
        return $detailedCart;
    }
}