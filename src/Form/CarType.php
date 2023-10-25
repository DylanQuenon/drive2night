<?php

namespace App\Form;

use App\Entity\Cars;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CarType extends AbstractType
{
    private function getConfiguration(string $label, string $placeholder, array $options = []): array
    {
        $configuration = [
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder,
            ],
        ];
    
        // Vérifiez si des options supplémentaires ont été fournies
        if (!empty($options)) {
            $configuration = array_merge_recursive($configuration, $options);
        }
    
        return $configuration;
    }
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
            ->add('owners', IntegerType::class, $this->getConfiguration('Km', 'Inscrivez le nombre de propriétaires'))
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cars::class,
        ]);
    }
}
