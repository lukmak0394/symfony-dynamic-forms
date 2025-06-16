<?php

namespace App\Controller;

use App\Entity\Template;
use App\Entity\TemplateField;
use App\Form\TemplateFieldType;
use App\Form\TemplateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TemplateController extends AbstractController
{
    #[Route('/template/new', name: 'template_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $template = new Template();
        $form = $this->createForm(TemplateType::class, $template);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($template);
            $em->flush();
            $this->addFlash('success', 'Szablon został utworzony pomyślnie.');
            return $this->redirectToRoute('template_list');
        }

        return $this->render('template/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/template/list', name: 'template_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $templates = $em->getRepository(Template::class)->findAll();

        return $this->render('template/list.html.twig', [
            'templates' => $templates,
        ]);
    }

    #[Route('/template/{id}/add-field', name: 'template_add_field')]
    public function addField(Template $template, Request $request, EntityManagerInterface $em): Response
    {
        $field = new TemplateField();
        $field->setTemplate($template);

        $form = $this->createForm(TemplateFieldType::class, $field);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($field);
            $em->flush();
            $this->addFlash('success', 'Pole zostało dodane do szablonu.');
            return $this->redirectToRoute('template_add_field', ['id' => $template->getId()]);
        }

        return $this->render('template/add_field.html.twig', [
            'template' => $template,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/template/{id}/edit', name: 'template_edit')]
    public function edit(Template $template, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TemplateType::class, $template);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('template_list');
        }

        return $this->render('template/edit.html.twig', [
            'form' => $form->createView(),
            'template' => $template,
        ]);
    }

    #[Route('/template/{id}/delete', name: 'template_delete', methods: ['POST'])]
    public function delete(Template $template, EntityManagerInterface $em, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('delete_template_' . $template->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Nieprawidłowy token.');
            return $this->redirectToRoute('template_list');
        }

        try {

            foreach ($template->getEntries() as $entry) {
                foreach ($entry->getEntryFieldValues() as $value) {
                    $em->remove($value);
                }
                $em->remove($entry);
            }

            foreach ($template->getTemplateFields() as $field) {
                $em->remove($field);
            }

            $em->remove($template);
            $em->flush();

            $this->addFlash('success', 'Szablon oraz wszystkie powiązane dane zostały usunięte.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Błąd podczas usuwania szablonu: ' . $e->getMessage());
        }

        return $this->redirectToRoute('template_list');
    }

    #[Route('/template/{id}/fields', name: 'template_fields')]
    public function fields(Template $template): Response
    {
        $fields = $template->getTemplateFields();

        return $this->render('template/fields.html.twig', [
            'template' => $template,
            'fields' => $fields,
        ]);
    }
}
