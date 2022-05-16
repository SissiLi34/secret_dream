<?php

namespace App\Controller;

use App\Cart\CartService;
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

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var CartService
     */
    protected $cartService;

    public function __construct(ProductRepository $productRepository, CartService $cartService)
    {
        $this->productRepository = $productRepository;
        $this->cartService = $cartService;
    }


    #[Route('/cart/add/{id}', name: 'app_cart_add', requirements: ["id" => "\d+"])]

    public function add($id, Request $request): Response
    {
        //0. Est ce que le produit existe ?
        $product = $this->productRepository->find($id);
        //S'il n'existe pas
        if (!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas");
        }
        //Je fais appel à la class CartService ou sont regrouppés tous les services et j'appelle l'id
        $this->cartService->add($id);

        //Si je veux supprimer le panier c'est:
        //$request->getSession()->remove('cart');

        //je me sers du Flash pour ajouter un msg d'action
        $this->addFlash('success', ["title" => "", "content" => "Le produit a bien été ajouté au panier"]);

//si dans url i y a returntoCart retourne vers la cart
        if ($request->query->get('returnToCart')) {
            return $this->redirectToRoute("cart_show");
        }


//si je n'ai pas l'info returnToCart je repars ur la page du produits
        return $this->redirectToRoute('product_show', [
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug()
        ]);
    }





    #[Route('/cart', name: 'cart_show')]
    //Je récupèe la session avec argumentResolver ainsi que le produit
    public function show()
    { 
        //J'injecte la méthode getdetailedCartItems de mon cartService
        $detailedCart = $this->cartService->getDetailedCartItems();

        $total = $this->cartService->getTotal();

        return $this->render('cart/index.html.twig', [
            //Je passe à twig une variable items qui est le tableau $detailedCart et le total
            'items' => $detailedCart,
            'total' => $total
        ]);
    }





    #[Route('/cart/delete/{id}', name: 'cart_delete', requirements: ["id" => "\d+"])]
    public function delete($id)
    {
        //je vérifie que le produit existe bien
        $product = $this->productRepository->find($id);
        //Si le produit n'existe pas je passe une exeption
        if (!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut pas être supprimé !");
        }
        //je passe à la function remove de cartService l'id à supprimer
        $this->cartService->remove($id);
        //J'envoie mon message de réussite
        $this->addFlash('success', ["title" => "", "content" => "Le produit a bien été supprimé du panier"]);


        //Et enfin je redirige vers le produit vérifier qu'il a bien été supprimé
        return $this->redirectToRoute("cart_show");
    }






    #[Route('/cart/decrement/{id}', name: 'cart_decrement', requirements: ["id" => "\d+"])]
    public function decrement($id)
    {
        //je vérifie que le produit existe bien
        $product = $this->productRepository->find($id);
        
        //Si le produit n'existe pas je passe une exeption
        if (!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut pas être décrémenté !");
        }

        $this->cartService->decrement($id);

                //J'envoie mon message de réussite
                $this->addFlash('success', ["title" => "", "content" => "Le produit a bien été retiré du panier"]);
               
        return $this->redirectToRoute("cart_show");
    }
}