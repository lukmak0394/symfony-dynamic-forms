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

use App\Repository\TemplateFieldRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TemplateFieldRepository::class)]
class TemplateField
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'templateFields')]
    private ?Template $template = null;

    #[ORM\Column(length: 255)]
    private ?string $systemName = null;

    #[ORM\Column(length: 255)]
    private ?string $displayName = null;

    #[ORM\Column]
    private ?bool $is_required = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(nullable: true)]
    private ?string $params = null;

    #[ORM\Column(type: 'integer')]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private int $position = 0;

    /**
     * @var Collection<int, EntryFieldValue>
     */
    #[ORM\OneToMany(targetEntity: EntryFieldValue::class, mappedBy: 'templateField')]
    private Collection $entryFieldValues;

    public function __construct()
    {
        $this->entryFieldValues = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTemplate(): ?Template
    {
        return $this->template;
    }

    public function setTemplate(?Template $template): static
    {
        $this->template = $template;

        return $this;
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

    public function isRequired(): ?bool
    {
        return $this->is_required;
    }

    public function setIsRequired(bool $is_required): static
    {
        $this->is_required = $is_required;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getParams(): ?array
    {
        if ($this->params === null) {
            return null;
        }

        $decoded = json_decode($this->params, true);
        return is_array($decoded) ? $decoded : null;
    }

    public function setParams(array|string|null $params): self
    {
        if (is_array($params)) {
            $this->params = json_encode($params, JSON_UNESCAPED_UNICODE);
        } elseif (is_string($params) || $params === null) {
            $this->params = $params;
        }

        return $this;
    }
    /**
     * @return Collection<int, EntryFieldValue>
     */
    public function getEntryFieldValues(): Collection
    {
        return $this->entryFieldValues;
    }

    public function addEntryFieldValue(EntryFieldValue $entryFieldValue): static
    {
        if (!$this->entryFieldValues->contains($entryFieldValue)) {
            $this->entryFieldValues->add($entryFieldValue);
            $entryFieldValue->setTemplateField($this);
        }

        return $this;
    }

    public function removeEntryFieldValue(EntryFieldValue $entryFieldValue): static
    {
        if ($this->entryFieldValues->removeElement($entryFieldValue)) {
            // set the owning side to null (unless already changed)
            if ($entryFieldValue->getTemplateField() === $this) {
                $entryFieldValue->setTemplateField(null);
            }
        }

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;
        return $this;
    }
}
