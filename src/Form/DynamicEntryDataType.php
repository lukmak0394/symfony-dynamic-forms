<?php

namespace App\Form;

use App\Entity\TemplateField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DynamicEntryDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var TemplateField[] $fields */
        $fields = $options['template_fields'];

        foreach ($fields as $field) {
            $name = 'field_' . $field->getId();
            $params = $field->getParams() ?? [];

            $type = match ($field->getType()) {
                'text' => TextType::class,
                'email' => EmailType::class,
                'url' => UrlType::class,
                'number', 'cost' => NumberType::class,
                'date' => DateType::class,
                'datetime' => DateTimeType::class,
                'select' => ChoiceType::class,
                default => TextType::class,
            };

            $options = [
                'label' => $field->getDisplayName(),
                'required' => $field->isRequired(),
                'constraints' => [],
            ];

            if ($field->getType() === 'text' && isset($params['max_length'])) {
                $options['attr']['maxlength'] = $params['max_length'];
                $options['constraints'][] = new Assert\Length([
                    'max' => $params['max_length'],
                    'maxMessage' => sprintf('Maksymalna długość to %d znaków.', $params['max_length']),
                ]);
            }

            if ($field->getType() === 'select' && is_array($params)) {
                $options['choices'] = array_combine($params, $params);
                $options['placeholder'] = '-- wybierz --';
            }

            if ($field->getType() === 'cost') {
                $options['scale'] = 2;
                $options['attr']['step'] = '0.01';
                $options['constraints'][] = new Assert\Type('numeric');
                $options['constraints'][] = new Assert\GreaterThanOrEqual(0);
            }

            if ($field->getType() === 'number') {
                $options['constraints'][] = new Assert\Type('numeric');
            }

            $builder->add($name, $type, $options);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'template_fields' => [],
        ]);
    }
}
