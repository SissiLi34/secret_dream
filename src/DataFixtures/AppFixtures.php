<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\User;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\DBAL\Driver\IBMDB2\Exception\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    //Je veux que ma class reçoive la fonction slugger dans mon constructeur
    protected $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
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

        $admin->setEmail("admin@gmail.com")
            ->setPassword("password")
            ->setFullName("Admin")
            ->getRoles(['ROLE_ADMIN']);

            //Et je persiste mon admin
            $manager->persist($admin);
            
        //Chaque itération de la boucle créée un user
        for ($u = 0; $u < 5; $u++) {
            $user = new User();
            //$u donne une suite de num
            $user->setEmail("user$u@gmail.com")
                ->setFullName($faker->name)
                ->setPassword("passeword");
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



        //Mettre le flush hors de la boucle pour ne faire qu'une requête et obtinmiser
        $manager->flush();
    }
}