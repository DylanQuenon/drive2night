<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;

class ApplicationType extends AbstractType
{
    protected function getConfiguration(string $label, string $placeholder, array $options = []): array
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
}



?>