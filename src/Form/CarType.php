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
            'label' => "Model",
            'attr' => [
                'placeholder' => "Car model",
            ]
        ])
        ->add('brand', TextType::class, $this->getConfiguration('Brand', 'Car brand'))
        ->add('slug', TextType::class, $this->getConfiguration('Slug', 'Web address (automatic)', [
            'required' => false
        ]))
        ->add('coverImage', UrlType::class, $this->getConfiguration("Image URL", "Provide the image URL"))
        ->add('km', IntegerType::class, $this->getConfiguration('Kilometers', 'Enter the car mileage'))
        ->add('price', MoneyType::class, $this->getConfiguration('Price', 'Enter the car price'))
        ->add('owners', IntegerType::class, $this->getConfiguration('Owners', 'Enter the number of owners'))
        ->add('cylinder', IntegerType::class, $this->getConfiguration('Cylinder', "Enter the engine displacement (e.g., 1.6L)"))
        ->add('power', IntegerType::class, $this->getConfiguration('Power', "Enter the car power"))
        ->add('fuel', ChoiceType::class, $this->getConfiguration("Fuel", "Choose the fuel type", [
            'choices' => [
                'Gasoline' => 'Gasoline',
                'Diesel' => 'Diesel',
                'Electric' => 'Electric',
                'Hybrid' => 'Hybrid',
            ],
        ]))
        ->add('year', IntegerType::class, $this->getConfiguration('Year', "Enter the original year of the car"))
        ->add('transmission', ChoiceType::class, $this->getConfiguration("Transmission", "Select the transmission type", [
            'choices' => [
                'manual' => 'Manual',
                'auto' => 'Automatic'
            ],
        ]))
        ->add('content', TextareaType::class, $this->getConfiguration('Content', 'Provide a description of your car'))
        ->add('options', TextareaType::class, $this->getConfiguration('Options', 'Provide the options of your car'))
        ->add('slugBrand', TextType::class, $this->getConfiguration('Brand Slug', 'Web address (automatic)', [
            'required' => false
        ]))
        ->add('images', CollectionType::class, [
            'entry_type' => ImageType::class,
            'allow_add' => true,
            'allow_delete' => true
        ])
    ;}
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cars::class,
        ]);
    }
}
