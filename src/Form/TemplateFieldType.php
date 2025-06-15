<?php

namespace App\Form;

use App\Entity\TemplateField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\Constraints as Assert;

class TemplateFieldType extends AbstractType
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
            ->add('type', ChoiceType::class, [
                'label' => 'Typ pola',
                'choices' => [
                    'Tekst' => 'text',
                    'Data' => 'date',
                    'Data + czas' => 'datetime',
                    'Liczba' => 'number',
                    'Koszt' => 'koszt',
                    'E-mail' => 'email',
                    'URL' => 'url',
                    'Lista wyboru (select)' => 'select',
                ],
            ])
            ->add('is_required', CheckboxType::class, [
                'label' => 'Wymagane',
                'required' => false,
                'help' => 'Odznacz jeżeli pole ma być opcjonalne.',
                'data' => true,
            ])
            ->add('params', TextareaType::class, [
                'label' => 'Parametry',
                'required' => false,
                'help' => 'Dla pola wyboru (select) podaj wartości oddzielone przecinkami, np. "Opcja 1, Opcja 2". Dla pola tekstowego (text) podaj maksymalną długość jako liczbę, np. "255".',
            ]);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {

            $field = $event->getData();
            $form = $event->getForm();

            $type = $form->get('type')->getData();
            $params_raw = $form->get('params')->getData();

            try {
                if ($type === 'select') {
                    $params = array_filter(array_map('trim', explode(',', $params_raw)));
                    if (empty($params)) {
                        throw new \Exception('Podaj przynajmniej jedną wartość (oddziel przecinkami) dla pola wyboru.');
                    }
                    $field->setParams($params);
                } else if ($type === 'text') {
                    if (!is_numeric($params_raw)) {
                        $params_raw = 255; // Default max length if not provided
                    }
                    $field->setParams(['max_length' => (int) $params_raw]);
                } else {
                    $field->setParams([]);
                }

                $event->setData($field);
            } catch (\Exception $e) {
                $form->get('params')->addError(new FormError('Nieprawidłowe parametry pola: ' . $e->getMessage()));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TemplateField::class,
        ]);
    }
}
