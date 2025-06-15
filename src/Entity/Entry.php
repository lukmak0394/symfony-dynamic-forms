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

use App\Repository\EntryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntryRepository::class)]
class Entry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'entries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Template $template = null;

    /**
     * @var Collection<int, EntryFieldValue>
     */
    #[ORM\OneToMany(targetEntity: EntryFieldValue::class, mappedBy: 'entry')]
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
            $entryFieldValue->setEntry($this);
        }

        return $this;
    }

    public function removeEntryFieldValue(EntryFieldValue $entryFieldValue): static
    {
        if ($this->entryFieldValues->removeElement($entryFieldValue)) {
            // set the owning side to null (unless already changed)
            if ($entryFieldValue->getEntry() === $this) {
                $entryFieldValue->setEntry(null);
            }
        }

        return $this;
    }
}
