<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use App\Repository\EntryFieldValueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntryFieldValueRepository::class)]
class EntryFieldValue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'entryFieldValues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Entry $entry = null;

    #[ORM\ManyToOne(inversedBy: 'entryFieldValues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TemplateField $templateField = null;

    #[ORM\Column(length: 255)]
    private ?string $value = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntry(): ?Entry
    {
        return $this->entry;
    }

    public function setEntry(?Entry $entry): static
    {
        $this->entry = $entry;

        return $this;
    }

    public function getTemplateField(): ?TemplateField
    {
        return $this->templateField;
    }

    public function setTemplateField(?TemplateField $templateField): static
    {
        $this->templateField = $templateField;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }
}
