<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;





class PurchasesListController extends AbstractController
{

    #[Route('/purchases', name: 'purchase_index')]
    #[IsGranted("ROLE_USER", message: "Vous devez être connecté pour accéder à vos commandes")]

    //doit me retourner la liste des commandes de l'utilisateur actuellement connecté
    public function index()
    {

        // 1. Je m'assure que la personne est connectée, sinon redirection vers la page d'accueil -> class Sécurity
        //Avec cette @var je m'assure que vscode prenne en compte l'entity de User avec la bonne class (User) que j'importe
        /**
         * @var User
         */
        $user = $this->getUser();




        //J'ai besoin de savoir à quel url je veux retourner, mais je ne veux pas la passer en dur, je veux générer une url en fonction du nom d'une route

        //Un routeur peut générer un URL à partir d'un nom de route
        //$url = $this->router->generate('app_shop_shop');
        //Redirection -> RedirectctResponse
        //return new RedirectResponse($url);

        //  Avec le 1. j'arrive ici:2. Je veux savoir qui est connecté-> class Sécuriy

        //3. Je veux passer à l'utilisateur connecté la page twig afin qu'il voit ses commandes-> Environment de Twig + class Response
        return $this->render('purchase/index.html.twig', [
            'purchases' => $user->getPurchases()
        ]);
    }
}