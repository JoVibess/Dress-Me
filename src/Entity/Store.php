<?php

namespace App\Entity;

use App\Repository\StoreRepository;
use App\Traits\CreatedAtUpdatedAtEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StoreRepository::class)]
#[ORM\Table(name: 'stores')]
#[ORM\UniqueConstraint(name: 'UNIQ_STORE_WEBSITE', fields: ['website'])]
#[ORM\HasLifecycleCallbacks]
class Store
{
    use CreatedAtUpdatedAtEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'stores')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 150)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $website = null;

    #[ORM\Column]
    private bool $isActive = true;

    #[ORM\Column]
    private int $anonymousDailyQuota = 20;

    /**
     * @var Collection<int, ApiToken>
     */
    #[ORM\OneToMany(targetEntity: ApiToken::class, mappedBy: 'store')]
    private Collection $apiTokens;

    /**
     * @var Collection<int, TryOnRequest>
     */
    #[ORM\OneToMany(targetEntity: TryOnRequest::class, mappedBy: 'store')]
    private Collection $tryOnRequests;

    public function __construct()
    {
        $this->apiTokens = new ArrayCollection();
        $this->tryOnRequests = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(string $website): static
    {
        $this->website = $website;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getAnonymousDailyQuota(): int
    {
        return $this->anonymousDailyQuota;
    }

    public function setAnonymousDailyQuota(int $anonymousDailyQuota): static
    {
        $this->anonymousDailyQuota = $anonymousDailyQuota;

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
            $apiToken->setStore($this);
        }

        return $this;
    }

    public function removeApiToken(ApiToken $apiToken): static
    {
        if ($this->apiTokens->removeElement($apiToken)) {
            if ($apiToken->getStore() === $this) {
                $apiToken->setStore(null);
            }
        }

        return $this;
    }

    public function getActiveApiToken(): ?ApiToken
    {
        foreach ($this->apiTokens as $apiToken) {
            if ($apiToken->isActive()) {
                return $apiToken;
            }
        }

        return null;
    }

    /**
     * @return Collection<int, TryOnRequest>
     */
    public function getTryOnRequests(): Collection
    {
        return $this->tryOnRequests;
    }
}
