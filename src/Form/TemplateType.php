<?php

namespace App\Form;

use App\Entity\Template;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class TemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('systemName', TextType::class, [
                'label' => 'Nazwa systemowa (camelCase)',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^[a-z]+(?:[A-Z][a-z0-9]*)+$/',
                        'message' => 'Nazwa systemowa musi mieć format camelCase.',
                    ])
                ],
            ])
            ->add('displayName', TextType::class, [
                'label' => 'Nazwa wyświetlana',
            ])
            ->add('is_active', CheckboxType::class, [
                'label' => 'Aktywny',
                'required' => false,
                'help' => 'Odznacz jeżeli szablon ma nie być używany.',
                'data' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Template::class,
        ]);
    }
}
