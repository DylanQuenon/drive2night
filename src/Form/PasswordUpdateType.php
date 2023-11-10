<?php

namespace App\Form;

use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class PasswordUpdateType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('oldPassword', PasswordType::class, $this->getConfiguration("Old password", "Give your current password"))
        ->add('newPassword', PasswordType::class, $this->getConfiguration("New password", "Give your new password"))
        ->add('confirmPassword', PasswordType::class, $this->getConfiguration("Confirm password", "Confirm your current password"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}