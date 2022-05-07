<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //Je construis mon formulaire
        //J'importe la class que je souhaite
        
        $builder
        ->add('name', TextType::class, [
            //je passe tous mes paramètrse dans le tableau
            'label' => 'Nom du produit',
            'attr' => [
                //Form bootstrap 
                'placeholder' => 'Tapez le nom du produit'
            ]
        ])
            //Description avec textarea
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

            ->add('mainPicture', UrlType::class, [
                'label' => 'Image du produit',
                'attr' => ['placeholder' => 'Tapez une URL d\'image']
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

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            //Le formulaire qui sera issu de cette class de formulaire travaillera sur l'entité Product
            'data_class' => Product::class,
        ]);
    }
}