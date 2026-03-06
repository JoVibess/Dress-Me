<?php

namespace App\Entity;

use App\Enum\TypeLot;
use App\Repository\LotCreditRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LotCreditRepository::class)]
class LotCredit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lotCredits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Abonnement $abonnement = null;

    #[ORM\Column(length: 20, enumType: TypeLot::class)]
    private ?TypeLot $type_lot = null;

    #[ORM\Column]
    private ?int $montant_initial = null;

    #[ORM\Column]
    private ?int $montant_restant = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $date_expiration = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAbonnement(): ?Abonnement
    {
        return $this->abonnement;
    }

    public function setAbonnement(?Abonnement $abonnement): static
    {
        $this->abonnement = $abonnement;

        return $this;
    }

    public function getTypeLot(): ?TypeLot
    {
        return $this->type_lot;
    }

    public function setTypeLot(TypeLot $type_lot): static
    {
        $this->type_lot = $type_lot;

        return $this;
    }

    public function getMontantInitial(): ?int
    {
        return $this->montant_initial;
    }

    public function setMontantInitial(int $montant_initial): static
    {
        $this->montant_initial = $montant_initial;

        return $this;
    }

    public function getMontantRestant(): ?int
    {
        return $this->montant_restant;
    }

    public function setMontantRestant(int $montant_restant): static
    {
        $this->montant_restant = $montant_restant;

        return $this;
    }

    public function getDateExpiration(): ?\DateTimeImmutable
    {
        return $this->date_expiration;
    }

    public function setDateExpiration(?\DateTimeImmutable $date_expiration): static
    {
        $this->date_expiration = $date_expiration;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
