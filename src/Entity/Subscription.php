<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use App\Traits\CreatedAtUpdatedAtEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Subscription
{
    use CreatedAtUpdatedAtEntity;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_EXPIRED = 'expired';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'subscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'subscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Offer $offer = null;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $startsAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $endsAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, ApiToken>
     */
    #[ORM\OneToMany(targetEntity: ApiToken::class, mappedBy: 'subscription')]
    private Collection $apiTokens;

    /**
     * @var Collection<int, CreditBatch>
     */
    #[ORM\OneToMany(targetEntity: CreditBatch::class, mappedBy: 'subscription')]
    private Collection $creditBatches;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->apiTokens = new ArrayCollection();
        $this->creditBatches = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getOffer(): ?Offer
    {
        return $this->offer;
    }

    public function setOffer(?Offer $offer): static
    {
        $this->offer = $offer;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getStartsAt(): ?\DateTimeImmutable
    {
        return $this->startsAt;
    }

    public function setStartsAt(\DateTimeImmutable $startsAt): static
    {
        $this->startsAt = $startsAt;

        return $this;
    }

    public function getEndsAt(): ?\DateTimeImmutable
    {
        return $this->endsAt;
    }

    public function setEndsAt(?\DateTimeImmutable $endsAt): static
    {
        $this->endsAt = $endsAt;

        return $this;
    }

    /**
     * @return Collection<int, ApiToken>
     */
    public function getApiTokens(): Collection
    {
        return $this->apiTokens;
    }

    public function addApiToken(ApiToken $apiToken): static
    {
        if (!$this->apiTokens->contains($apiToken)) {
            $this->apiTokens->add($apiToken);
            $apiToken->setSubscription($this);
        }

        return $this;
    }

    public function removeApiToken(ApiToken $apiToken): static
    {
        if ($this->apiTokens->removeElement($apiToken)) {
            // set the owning side to null (unless already changed)
            if ($apiToken->getSubscription() === $this) {
                $apiToken->setSubscription(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CreditBatch>
     */
    public function getCreditBatches(): Collection
    {
        return $this->creditBatches;
    }

    public function addCreditBatch(CreditBatch $creditBatch): static
    {
        if (!$this->creditBatches->contains($creditBatch)) {
            $this->creditBatches->add($creditBatch);
            $creditBatch->setSubscription($this);
        }

        return $this;
    }

    public function removeCreditBatch(CreditBatch $creditBatch): static
    {
        if ($this->creditBatches->removeElement($creditBatch)) {
            // set the owning side to null (unless already changed)
            if ($creditBatch->getSubscription() === $this) {
                $creditBatch->setSubscription(null);
            }
        }

        return $this;
    }
}
