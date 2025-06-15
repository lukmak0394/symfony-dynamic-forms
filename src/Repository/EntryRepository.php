<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\Entry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Template;

/**
 * @extends ServiceEntityRepository<Entry>
 */
class EntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entry::class);
    }

    public function findByTemplateAndCriteria(Template $template, array $criteria)
    {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.template = :template')
            ->setParameter('template', $template);

        $index = 0;

        foreach ($template->getTemplateFields() as $field) {
            $key_base = 'field_' . $field->getId();
            $field_type = $field->getType();

            if (in_array($field_type, ['date', 'datetime'], true)) {
                $index = $this->applyDateCriteria($qb, $field, $criteria, $key_base, $field_type, $index);
            } else {
                $index = $this->applyTextCriteria($qb, $field, $criteria, $key_base, $index);
            }
        }

        return $qb->getQuery()->getResult();
    }

    private function applyDateCriteria($qb, $field, $criteria, string $key_base, string $field_type, int $index): int
    {
        $formats = [
            'date' => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
        ];

        foreach (['from', 'to'] as $bound) {
            $value = $criteria["{$key_base}_{$bound}"] ?? null;

            if ($value instanceof \DateTimeInterface) {
                $alias = 'efv' . $index;
                $param_suffix = "{$bound}_{$index}";
                $field_param = "field_{$param_suffix}";
                $value_param = "value_{$param_suffix}";

                $qb->join('e.entryFieldValues', $alias, 'WITH', "$alias.templateField = :$field_param")
                    ->andWhere("$alias.value " . ($bound === 'from' ? '>=' : '<=') . " :$value_param")
                    ->setParameter($field_param, $field->getId())
                    ->setParameter($value_param, $value->format($formats[$field_type]));

                $index++;
            }
        }

        return $index;
    }

    private function applyTextCriteria($qb, $field, $criteria, string $key_base, int $index): int
    {
        $value = $criteria[$key_base] ?? null;

        if (!empty($value)) {
            $alias = 'efv' . $index;
            $field_param = "field_{$index}";
            $value_param = "value_{$index}";

            $qb->join('e.entryFieldValues', $alias, 'WITH', "$alias.templateField = :$field_param")
                ->andWhere($qb->expr()->like("$alias.value", ":$value_param"))
                ->setParameter($field_param, $field->getId())
                ->setParameter($value_param, '%' . $value . '%');

            $index++;
        }

        return $index;
    }
}
