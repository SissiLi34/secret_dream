<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Purchase;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\DBAL\Driver\IBMDB2\Exception\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    //Je veux que ma class reçoive la fonction slugger dans mon constructeur
    protected $slugger;
    protected $passwordHasher;

    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $passwordHasher)
    {
        $this->slugger = $slugger;
        $this->passwordHasher = $passwordHasher;
    }

    //objectManager = entityManagerInterface
    public function load(ObjectManager $manager): void
    {
        //Création d'une instance faker qui va générer des noms/adresse/villes etc en français
        $faker = Faker\Factory::create('fr_FR');
        //J'ajoute des méthodes avec cette instance:
        $faker->addProvider(new \Liior\Faker\Prices($faker));
        //J'ajoute le provider commerce qui va ajouter des nom de produits cohérents
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

        //Je créé l'admin avec le role ADMIN
        $admin = new User;
        //Dans le hash je vais avoir le mdp de façon illisible, il est encodé
        //J'ai passé $admin pour que l'encoder comprenne qu'il vient de la class user et aller voir dans le yaml la conduite à tenir (auto)
    
        $admin->setEmail("admin@gmail.com")
            ->setFullName("Admin")
            ->setRoles(['ROLE_ADMIN'])            
            ->setPassword($this->passwordHasher->hashPassword($admin, 'password'));

        //Et je persiste mon admin
        $manager->persist($admin);

        //je créé un tableau vide pour y rajouter les user qui auront une commande affectée         
        $users = [];
        
        //Chaque itération de la boucle créée un user
        for ($u = 0; $u < 5; $u++) {
            $user = new User();

            

            //$u donne une suite de num
            $user->setEmail("user$u@gmail.com")
                ->setFullName($faker->name)
                ->setPassword($this->passwordHasher->hashPassword($user, "password"));
            //Dans le tableau vide que j'ai créé viendra se mettre les users avec commande
            $users[] = $user;
                
            //au bout de la boucle le manager va persister les 5 new utilisateurs
            $manager->persist($user);
            
        }





        // Création de 3 catégories
        for ($c = 0; $c < 3; $c++) {
            $category = new Category;
            $category->setName($faker->department)->setSlug(strtolower($this->slugger->slug($category->getName())));

            $manager->persist($category);

            //Je persiste entre 15 et 20 produits par category
            for ($p = 0; $p < mt_rand(15, 20); $p++) {
                $product = new Product;
                $product->setName($faker->productName)
                    //J'applique ma nouvelle méthode Faker pur avoir un prix cohérent dans les conditions()
                    ->setPrice($faker->price(4000, 20000))
                    //Je prends le nom du produit et je le transforme en slug dans majuscule
                    ->setSlug(strtolower($this->slugger->slug($product->getName())))
                    //Je rajoute une catégorie
                    ->setCategory($category)
                    //je rajoute un paragraphe
                    ->setShortDescription($faker->paragraph())
                    //Je rajote une image
                    ->setMainPicture($faker->imageUrl(200, 200, true));


                $manager->persist($product);
            }
        }

//tant que p est inferieur à ..
for ($p=0; $p < mt_rand(20, 40) ; $p++) { 
    $purchase = new Purchase;

    $purchase->setFullName($faker->name)
            ->setAddress($faker->streetAddress)
            ->setPostalCode($faker->postcode)
            ->setCity($faker->city)
            ->setUser($faker->randomElement($users))
            ->setTotal(mt_rand(2000, 30000))
            ->setPurchasedAt($faker->datetime_immutable);
    //faker va me fournir 90% de commnde payé et le reste en attente
    if ($faker->boolean(90)) {
        //pr défaut j'ai mis pending donc je ne mets pas de condition puisque si il n'est pas dans le 90 il serz en attente de paiement
        $purchase->setStatus(Purchase::STATUS_PAID);
        //Il me reste à lier un user à cette purchase
    }
         $manager->persist($purchase);   
}

        //Mettre le flush hors de la boucle pour ne faire qu'une requête et obtinmiser
        $manager->flush();
    }
}