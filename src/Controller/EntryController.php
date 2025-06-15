<?php

namespace App\Controller;

use App\Entity\Entry;
use App\Entity\EntryFieldValue;
use App\Form\DynamicEntryDataType;
use App\Form\EntryType;
use App\Repository\EntryRepository;
use App\Repository\TemplateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\DynamicSearchFormType;

class EntryController extends AbstractController
{
    #[Route('/entry/new', name: 'entry_new')]
    public function new(Request $request, TemplateRepository $templateRepo): Response
    {
        $form = $this->createForm(EntryType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $template = $form->get('template')->getData();
            return $this->redirectToRoute('entry_fill', ['template_id' => $template->getId()]);
        }

        $templates = $templateRepo->findBy(['is_active' => true]);

        if (!$templates) {
            $this->addFlash('info', 'Nie znaleziono aktywnych szablonów. Utwórz szablon przed dodaniem wpisu.');
            return $this->redirectToRoute('template_new');
        }

        return $this->render('entry/new.html.twig', [
            'form' => $form->createView(),
            'templates' => $templates,
        ]);
    }

    #[Route('/entry/fill/{template_id}', name: 'entry_fill')]
    public function fill(
        int $template_id,
        Request $request,
        TemplateRepository $templateRepo,
        EntityManagerInterface $em
    ): Response {

        $template = $templateRepo->find($template_id);

        if (!$template) {
            throw $this->createNotFoundException('Nie znaleziono szablonu.');
        }

        $entry = new Entry();
        $entry->setTemplate($template);

        $form = $this->createForm(DynamicEntryDataType::class, null, [
            'template_fields' => $template->getTemplateFields(),
        ]);

        $form->handleRequest($request);

        $errors = [];

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($template->getTemplateFields() as $field) {
                $submitted_name = 'field_' . $field->getId();
                $value = $form->get($submitted_name)->getData();

                if (empty($value)) {
                    if (!$field->isRequired()) {
                        continue;
                    }

                    $errors[] = sprintf('Pole "%s" jest wymagane.', $field->getDisplayName());
                    continue;
                }

                $fieldValue = new EntryFieldValue();
                $fieldValue->setEntry($entry);
                $fieldValue->setTemplateField($field);

                if ($value instanceof \DateTimeInterface) {
                    $value = $field->getType() === 'date'
                        ? $value->format('Y-m-d')
                        : $value->format('Y-m-d H:i:s');
                } elseif (is_array($value)) {
                    $value = json_encode($value);
                } else {
                    $value = (string) $value;
                }

                $fieldValue->setValue($value);
                $entry->addEntryFieldValue($fieldValue);
                $em->persist($fieldValue);
            }

            if (empty($errors)) {
                $em->persist($entry);
                $em->flush();
                return $this->redirectToRoute('entry_show', ['id' => $entry->getId()]);
            }
        }

        return $this->render('entry/fill.html.twig', [
            'template' => $template,
            'form' => $form->createView(),
            'errors' => $errors,
        ]);
    }

    #[Route('/entry/{id}/edit', name: 'entry_edit')]
    public function edit(
        Entry $entry,
        Request $request,
        EntityManagerInterface $em,
        TemplateRepository $templateRepo
    ): Response {

        $template_id_from_form = $request->request->get('template_id');
        $current_template_id = $entry->getTemplate()->getId();

        $template = null;
        $template_fields = [];

        if ($template_id_from_form && (int) $template_id_from_form !== $current_template_id) {
            $new_template = $templateRepo->find($template_id_from_form);

            if (!$new_template) {
                $this->addFlash('error', 'Nie znaleziono szablonu o podanym ID.');
                return $this->redirectToRoute('entry_edit', ['id' => $entry->getId()]);
            }

            $entry->setTemplate($new_template);
            $em->persist($entry);
            $em->flush();
            $em->refresh($entry);
            $template = $new_template;
            $template_fields = $template->getTemplateFields();
        } else {
            $template = $entry->getTemplate();
            $template_fields = $template->getTemplateFields();
        }

        $data = [];
        foreach ($entry->getEntryFieldValues() as $value) {
            $field = $value->getTemplateField();
            $raw = $value->getValue();
            $key = 'field_' . $field->getId();

            if (in_array($field->getType(), ['date', 'datetime'], true)) {
                $decoded = json_decode($raw, true);
                if (is_array($decoded) && isset($decoded['date'])) {
                    $data[$key] = new \DateTimeImmutable($decoded['date']);
                } else {
                    $data[$key] = new \DateTimeImmutable($raw);
                }
            } else {
                $data[$key] = $raw;
            }
        }


        $form = $this->createForm(DynamicEntryDataType::class, $data, [
            'template_fields' => $template_fields,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($entry->getEntryFieldValues() as $old) {
                $em->remove($old);
            }

            foreach ($template_fields as $field) {
                $submitted_name = 'field_' . $field->getId();
                $value = $form->get($submitted_name)->getData();

                if (empty($value)) {
                    if (!$field->isRequired()) {
                        continue;
                    }
                    continue;
                }

                // Normalizacja wartości przed zapisem
                if ($value instanceof \DateTimeInterface) {
                    $value = $field->getType() === 'date'
                        ? $value->format('Y-m-d')
                        : $value->format('Y-m-d H:i:s');
                } elseif (is_array($value)) {
                    $value = json_encode($value);
                } else {
                    $value = (string) $value;
                }

                $field_value = new EntryFieldValue();
                $field_value->setEntry($entry);
                $field_value->setTemplateField($field);
                $field_value->setValue($value);

                $entry->addEntryFieldValue($field_value);
                $em->persist($field_value);
            }


            $em->flush();

            return $this->redirectToRoute('entry_show', ['id' => $entry->getId()]);
        }

        $templates = $templateRepo->findBy(['is_active' => true]);

        return $this->render('entry/edit.html.twig', [
            'entry' => $entry,
            'form' => $form->createView(),
            'templates' => $templates,
        ]);
    }


    #[Route('/entries', name: 'entry_list')]
    public function list(Request $request, EntryRepository $entryRepo, TemplateRepository $templateRepo): Response
    {
        $template_id = $request->query->get('template_id');
        $entries = $entryRepo->findBy(['template' => $template_id]);
        $templates = $templateRepo->findAll();
        return $this->render('entry/list.html.twig', [
            'entries' => $entries,
            'templates' => $templates,
            'current_template_id' => $template_id,
        ]);
    }

    #[Route('/entry/{id}', name: 'entry_show')]
    public function show(Entry $entry): Response
    {
        $fields = $entry->getTemplate()->getTemplateFields();

        $values = [];
        foreach ($entry->getEntryFieldValues() as $field_value) {
            $key = $field_value->getTemplateField()->getId();
            $values[$key] = $field_value->getValue();
        }

        return $this->render('entry/show.html.twig', [
            'entry' => $entry,
            'fields' => $fields,
            'values' => $values,
        ]);
    }

    #[Route('/entries/{systemName}/search', name: 'entry_search')]
    public function search(
        string $systemName,
        TemplateRepository $templateRepo,
        EntryRepository $entryRepo,
        Request $request
    ): Response {
        $template = $templateRepo->findOneBy(['systemName' => $systemName]);

        if (!$template) {
            throw $this->createNotFoundException('Nie znaleziono szbalonu.');
        }

        $form = $this->createForm(DynamicSearchFormType::class, null, [
            'template_fields' => $template->getTemplateFields(),
        ]);

        $form->handleRequest($request);

        $criteria = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            foreach ($template->getTemplateFields() as $field) {
                $key = 'field_' . $field->getId();

                switch ($field->getType()) {
                    case 'date':
                    case 'date':
                    case 'datetime':
                        $from = $data[$key . '_from'] ?? null;
                        $to = $data[$key . '_to'] ?? null;

                        if ($from) {
                            $criteria[$key . '_from'] = $from;
                        }
                        if ($to) {
                            $criteria[$key . '_to'] = $to;
                        }
                        break;

                    default:
                        $value = $data[$key] ?? null;
                        if (!empty($value)) {
                            $criteria[$key] = $value;
                        }
                        break;
                }
            }
        }

        $entries = $entryRepo->findByTemplateAndCriteria($template, $criteria);
        $all_templates = $templateRepo->findAll();

        return $this->render('entry/search.html.twig', [
            'template' => $template,
            'form' => $form->createView(),
            'entries' => $entries,
            'templates' => $all_templates,
        ]);
    }
}
