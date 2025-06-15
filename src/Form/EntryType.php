<?php

namespace App\Form;

use App\Entity\Template;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('template', EntityType::class, [
            'class' => Template::class,
            'choice_label' => 'displayName',
            'label' => 'Wybierz szablon',
            'placeholder' => '-- Wybierz szablon --',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // entry nie jest powiązany z encją na tym etapie
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
