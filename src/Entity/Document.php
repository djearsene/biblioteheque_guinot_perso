<?php

namespace App\Entity;

use App\Repository\DocumentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentRepository::class)]
class Document
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    private ?Support $support = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    private ?Genre $genre = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    private ?Consultation $consultation = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    private ?Author $author = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    private ?Available $available = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $releasedate = null;


    #[ORM\OneToMany(mappedBy: 'document', targetEntity: Borrow::class)]
    private Collection $borrows;

    #[ORM\OneToMany(mappedBy: 'document', targetEntity: Images::class, cascade: ["persist"], orphanRemoval: true)]
    private Collection $images;

    public function __construct()
    {
        $this->borrows = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getSupport(): ?Support
    {
        return $this->support;
    }

    public function setSupport(?Support $support): static
    {
        $this->support = $support;

        return $this;
    }

    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    public function setGenre(?Genre $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function getConsultation(): ?Consultation
    {
        return $this->consultation;
    }

    public function setConsultation(?Consultation $consultation): static
    {
        $this->consultation = $consultation;

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getAvailable(): ?Available
    {
        return $this->available;
    }

    public function setAvailable(?Available $available): static
    {
        $this->available = $available;

        return $this;
    }

    public function getReleasedate(): ?\DateTimeInterface
    {
        return $this->releasedate;
    }

    public function setReleasedate(\DateTimeInterface $releasedate): static
    {
        $this->releasedate = $releasedate;

        return $this;
    }

    /**
     * @return Collection<int, Borrow>
     */
    public function getBorrows(): Collection
    {
        return $this->borrows;
    }

    public function addBorrow(Borrow $borrow): static
    {
        if (!$this->borrows->contains($borrow)) {
            $this->borrows->add($borrow);
            $borrow->setDocument($this);
        }

        return $this;
    }

    public function removeBorrow(Borrow $borrow): static
    {
        if ($this->borrows->removeElement($borrow)) {
            // set the owning side to null (unless already changed)
            if ($borrow->getDocument() === $this) {
                $borrow->setDocument(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Images>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImages(Images $images): static
    {
        if (!$this->images->contains($images)) {
            $this->images[] = $images;
            $images->setDocument($this);
        }

        return $this;
    }

    public function removeImages(Images $images): static
    {
        if ($this->images->removeElement($images)) {
            // set the owning side to null (unless already changed)
            if ($images->getDocument() === $this) {
                $images->setDocument(null);
            }
        }

        return $this;
    }
}
