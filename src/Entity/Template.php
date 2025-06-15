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

use App\Repository\TemplateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TemplateRepository::class)]
class Template
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $systemName = null;

    #[ORM\Column(length: 255)]
    private ?string $displayName = null;

    #[ORM\Column]
    private ?bool $is_active = null;

    /**
     * @var Collection<int, TemplateField>
     */
    #[ORM\OneToMany(targetEntity: TemplateField::class, mappedBy: 'template')]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $templateFields;

    /**
     * @var Collection<int, Entry>
     */
    #[ORM\OneToMany(targetEntity: Entry::class, mappedBy: 'template')]
    private Collection $entries;

    public function __construct()
    {
        $this->templateFields = new ArrayCollection();
        $this->entries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSystemName(): ?string
    {
        return $this->systemName;
    }

    public function setSystemName(string $systemName): static
    {
        $this->systemName = $systemName;

        return $this;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): static
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): static
    {
        $this->is_active = $is_active;

        return $this;
    }

    /**
     * @return Collection<int, TemplateField>
     */
    public function getTemplateFields(): Collection
    {
        return $this->templateFields;
    }

    public function addTemplateField(TemplateField $templateField): static
    {
        if (!$this->templateFields->contains($templateField)) {
            $this->templateFields->add($templateField);
            $templateField->setTemplate($this);
        }

        return $this;
    }

    public function removeTemplateField(TemplateField $templateField): static
    {
        if ($this->templateFields->removeElement($templateField)) {
            // set the owning side to null (unless already changed)
            if ($templateField->getTemplate() === $this) {
                $templateField->setTemplate(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Entry>
     */
    public function getEntries(): Collection
    {
        return $this->entries;
    }

    public function addEntry(Entry $entry): static
    {
        if (!$this->entries->contains($entry)) {
            $this->entries->add($entry);
            $entry->setTemplate($this);
        }

        return $this;
    }

    public function removeEntry(Entry $entry): static
    {
        if ($this->entries->removeElement($entry)) {
            if ($entry->getTemplate() === $this) {
                $entry->setTemplate(null);
            }
        }

        return $this;
    }
}
