<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DynamicSearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($options['template_fields'] as $field) {
            $key = 'field_' . $field->getId();

            switch ($field->getType()) {
                case 'select':
                    $builder->add($key, Type\ChoiceType::class, [
                        'choices' => array_combine($field->getParams(), $field->getParams()),
                        'required' => false,
                        'placeholder' => '--',
                        'label' => $field->getDisplayName(),
                    ]);
                    break;

                case 'date':
                    $builder->add($key . '_from', Type\DateType::class, [
                        'widget' => 'single_text',
                        'required' => false,
                        'label' => $field->getDisplayName() . ' (od)',

                    ]);
                    $builder->add($key . '_to', Type\DateType::class, [
                        'widget' => 'single_text',
                        'required' => false,
                        'label' => $field->getDisplayName() . ' (do)',
                    ]);
                    break;
                case 'datetime':
                    $builder->add($key . '_from', Type\DateTimeType::class, [
                        'widget' => 'single_text',
                        'required' => false,
                        'label' => $field->getDisplayName() . ' (od)',

                    ]);
                    $builder->add($key . '_to', Type\DateTimeType::class, [
                        'widget' => 'single_text',
                        'required' => false,
                        'label' => $field->getDisplayName() . ' (do)',
                    ]);
                    break;

                default:
                    $builder->add($key, Type\TextType::class, [
                        'required' => false,
                        'label' => $field->getDisplayName(),
                    ]);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('template_fields');
        $resolver->setDefaults([
            'method' => 'POST',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return ''; 
    }
}
