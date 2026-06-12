<?php

namespace App\Entity;

use App\Repository\ApiTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApiTokenRepository::class)]
class ApiToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'apiTokens')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Store $store = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $tokenValue = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $secretValue = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $activatedAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->isActive = false;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStore(): ?Store
    {
        return $this->store;
    }

    public function setStore(?Store $store): static
    {
        $this->store = $store;

        return $this;
    }

    public function getTokenValue(): ?string
    {
        return $this->tokenValue;
    }

    public function setTokenValue(string $tokenValue): static
    {
        $this->tokenValue = $tokenValue;

        return $this;
    }

    public function getSecretValue(): ?string
    {
        return $this->secretValue;
    }

    public function setSecretValue(?string $secretValue): static
    {
        $this->secretValue = $secretValue;

        return $this;
    }

    public function getActivatedAt(): ?\DateTimeImmutable
    {
        return $this->activatedAt;
    }

    public function setActivatedAt(?\DateTimeImmutable $activatedAt): static
    {
        $this->activatedAt = $activatedAt;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

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
