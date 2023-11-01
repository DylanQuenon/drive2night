<?php

namespace App\Form;

use App\Entity\Cars;
use App\Form\ImageType;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class CarType extends ApplicationType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('model', TextType::class, [
            'label' => "Modèle",
            'attr' => [
                'placeholder' => "Modèle de la voiture",
            ]
          
        ])
            ->add('brand', TextType::class, $this->getConfiguration('Marque', 'Marque de la voiture (automatique)'))
            ->add('slug',TextType::class, $this->getConfiguration('Slug', 'Adresse web (automatique)',[
                'required' => false
            ]))
            ->add('coverImage',UrlType::class, $this->getConfiguration("Url de l'image ", "Donnez l'url de l'image"))
            ->add('km', IntegerType::class, $this->getConfiguration('Km', 'Inscrivez le kilométrage de la voiture'))
            ->add('price', MoneyType::class, $this->getConfiguration('Prix', 'Inscrivez le prix de la voiture'))
            ->add('owners', IntegerType::class, $this->getConfiguration('Propriétaires', 'Inscrivez le nombre de propriétaires'))
            ->add('cylinder',IntegerType::class, $this->getConfiguration('Cylindrée', "Saisissez la cylindrée (par exemple, 1.6L)"))
            ->add('power',IntegerType::class, $this->getConfiguration('Puissance', "Saisissez la puissance de la voiture"))
            ->add('fuel', ChoiceType::class, $this->getConfiguration("Fuel", "Choisissez le carburant", [
                'choices' => [
                    'Gasoline' => 'Gasoline',
                    'Diesel' => 'Diesel',
                    'Electric' => 'Electric',
                    'Hybrid' => 'Hybrid',
                    // Ajoutez d'autres options de carburant au besoin
                ],
            ]))
            ->add('year',IntegerType::class, $this->getConfiguration('Année', "Saisissez l'année d'origine de la voiture "))
            ->add('transmission',ChoiceType::class,$this->getConfiguration("Transmission","Votre transmission",[
                'choices'=>[
                    'manual'=>'manual',
                    'auto'=>'auto'
                ],
            ]))
            ->add('content',TextareaType::class, $this->getConfiguration('Contenu','Donnez une description de votre voiture'))
            ->add('options',TextareaType::class, $this->getConfiguration('Options','Donnez les options de votre voiture'))
            ->add('slugBrand',TextType::class, $this->getConfiguration('Slug marque', 'Adresse web (automatique)',[
                'required' => false
            ]))
            ->add('images', CollectionType::class, [
                'entry_type' => ImageType::class,
                'allow_add' => true, // pour le data_prototype
                'allow_delete' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cars::class,
        ]);
    }
}
