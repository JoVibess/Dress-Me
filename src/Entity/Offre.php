<?php

namespace App\Entity;

use App\Enum\TypeOffre;
use App\Repository\OffreRepository;
use App\Traits\CreatedAtUpdatedAtEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OffreRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Offre
{
    use CreatedAtUpdatedAtEntity;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: false)]
    private ?string $prix = null;

    #[ORM\Column(length: 50, enumType: TypeOffre::class)]
    private ?TypeOffre $type_offre = null;

    #[ORM\Column]
    private ?int $nombre_credits = null;

    #[ORM\Column(nullable: true)]
    private ?int $duree_mois = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Abonnement>
     */
    #[ORM\OneToMany(targetEntity: Abonnement::class, mappedBy: 'offre')]
    private Collection $abonnements;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->abonnements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

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

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(?string $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getTypeOffre(): ?TypeOffre
    {
        return $this->type_offre;
    }

    public function setTypeOffre(TypeOffre $type_offre): static
    {
        $this->type_offre = $type_offre;

        return $this;
    }

    public function getNombreCredits(): ?int
    {
        return $this->nombre_credits;
    }

    public function setNombreCredits(int $nombre_credits): static
    {
        $this->nombre_credits = $nombre_credits;

        return $this;
    }

    public function getDureeMois(): ?int
    {
        return $this->duree_mois;
    }

    public function setDureeMois(?int $duree_mois): static
    {
        $this->duree_mois = $duree_mois;

        return $this;
    }

    /**
     * @return Collection<int, Abonnement>
     */
    public function getAbonnements(): Collection
    {
        return $this->abonnements;
    }

    public function addAbonnement(Abonnement $abonnement): static
    {
        if (!$this->abonnements->contains($abonnement)) {
            $this->abonnements->add($abonnement);
            $abonnement->setOffre($this);
        }

        return $this;
    }

    public function removeAbonnement(Abonnement $abonnement): static
    {
        if ($this->abonnements->removeElement($abonnement)) {
            // set the owning side to null (unless already changed)
            if ($abonnement->getOffre() === $this) {
                $abonnement->setOffre(null);
            }
        }

        return $this;
    }
}
