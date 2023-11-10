<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AccountType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('firstName', TextType::class ,$this->getConfiguration("First name", "Your first name..."))
        ->add('lastName', TextType::class ,$this->getConfiguration("Last name", "Your last name..."))
        ->add('email', EmailType::class, $this->getConfiguration("Email", "Your email address..."))
        ->add('introduction', TextType::class, $this->getConfiguration("Introduction", "Quick introduction"))
        ->add('description', TextareaType::class, $this->getConfiguration("Detailed description", "Introduce yourself with a little more detail"))

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}