<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Template;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

final class ApiController extends AbstractController
{
    #[Route('/api/templates/{id}/fields', name: 'api_template_fields', methods: ['GET'])]
    public function getTemplateFields(Template $template): JsonResponse
    {
        $fields = [];

        foreach ($template->getTemplateFields() as $field) {
            $fields[] = [
                'id' => $field->getId(),
                'systemName' => $field->getSystemName(),
                'displayName' => $field->getDisplayName(),
                'required' => $field->isRequired(),
                'type' => $field->getType(),
                'params' => $field->getParams(),
            ];
        }

        return new JsonResponse($fields);
    }

    #[Route('/api/{id}/reorder-fields', name: 'template_reorder_fields', methods: ['POST'])]
    public function reorderTemplateFields(Template $template, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['order']) || !is_array($data['order'])) {
            return new JsonResponse(['error' => 'Nieprawidłowe dane.'], 400);
        }

        foreach ($data['order'] as $item) {
            $field = $template->getTemplateFields()->filter(fn($f) => $f->getId() === (int)$item['id'])->first();
            if ($field) {
                $field->setPosition($item['position']);
                $em->persist($field);
            }
        }

        $em->flush();

        return new JsonResponse(['status' => 'success']);
    }
}
