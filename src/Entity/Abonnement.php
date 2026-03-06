<?php

namespace App\Entity;

use App\Repository\AbonnementRepository;
use App\Traits\CreatedAtUpdatedAtEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AbonnementRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Abonnement
{
    use CreatedAtUpdatedAtEntity;
    public const STATUT_ACTIF = 'actif';
    public const STATUT_RESILIE = 'resilie';
    public const STATUT_EXPIRE = 'expire';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'abonnements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne(inversedBy: 'abonnements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Offre $offre = null;

    #[ORM\Column(length: 20)]
    private ?string $statut = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateDebut = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateFin = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Token>
     */
    #[ORM\OneToMany(targetEntity: Token::class, mappedBy: 'abonnement')]
    private Collection $tokens;

    /**
     * @var Collection<int, LotCredit>
     */
    #[ORM\OneToMany(targetEntity: LotCredit::class, mappedBy: 'abonnement')]
    private Collection $lotCredits;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->tokens = new ArrayCollection();
        $this->lotCredits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getOffre(): ?Offre
    {
        return $this->offre;
    }

    public function setOffre(?Offre $offre): static
    {
        $this->offre = $offre;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeImmutable
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeImmutable $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeImmutable
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeImmutable $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * @return Collection<int, Token>
     */
    public function getTokens(): Collection
    {
        return $this->tokens;
    }

    public function addToken(Token $token): static
    {
        if (!$this->tokens->contains($token)) {
            $this->tokens->add($token);
            $token->setAbonnement($this);
        }

        return $this;
    }

    public function removeToken(Token $token): static
    {
        if ($this->tokens->removeElement($token)) {
            // set the owning side to null (unless already changed)
            if ($token->getAbonnement() === $this) {
                $token->setAbonnement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LotCredit>
     */
    public function getLotCredits(): Collection
    {
        return $this->lotCredits;
    }

    public function addLotCredit(LotCredit $lotCredit): static
    {
        if (!$this->lotCredits->contains($lotCredit)) {
            $this->lotCredits->add($lotCredit);
            $lotCredit->setAbonnement($this);
        }

        return $this;
    }

    public function removeLotCredit(LotCredit $lotCredit): static
    {
        if ($this->lotCredits->removeElement($lotCredit)) {
            // set the owning side to null (unless already changed)
            if ($lotCredit->getAbonnement() === $this) {
                $lotCredit->setAbonnement(null);
            }
        }

        return $this;
    }

}
