<?php

namespace App\Entity;

use App\Repository\TryOnRequestRepository;
use App\Traits\CreatedAtUpdatedAtEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TryOnRequestRepository::class)]
#[ORM\Table(name: 'try_on_request')]
#[ORM\UniqueConstraint(name: 'UNIQ_TRY_ON_JOB_ID', fields: ['jobId'])]
#[ORM\HasLifecycleCallbacks]
class TryOnRequest
{
    use CreatedAtUpdatedAtEntity;

    public const STATUS_RECEIVED = 'received';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REJECTED = 'rejected';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'tryOnRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Store $store = null;

    #[ORM\Column(length: 64)]
    private ?string $jobId = null;

    #[ORM\Column(length: 255)]
    private ?string $anonymousVisitorId = null;

    #[ORM\Column(length: 255)]
    private ?string $siteUrl = null;

    #[ORM\Column]
    private ?int $productId = null;

    #[ORM\Column(nullable: true)]
    private ?int $variationId = null;

    #[ORM\Column(length: 255)]
    private ?string $productTitle = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $productDescription = null;

    #[ORM\Column(length: 2048, nullable: true)]
    private ?string $productImageUrl = null;

    /**
     * @var string[]
     */
    #[ORM\Column(type: 'json')]
    private array $productCategories = [];

    #[ORM\Column]
    private bool $customerImageProvided = false;

    #[ORM\Column(length: 2048, nullable: true)]
    private ?string $customerImagePath = null;

    #[ORM\Column(nullable: true)]
    private ?int $requestedAnonymousDailyQuota = null;

    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_RECEIVED;

    #[ORM\Column]
    private int $creditsConsumed = 0;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $errorCode = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $errorMessage = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $providerRequestId = null;

    #[ORM\Column(length: 2048, nullable: true)]
    private ?string $generatedImagePath = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $completedAt = null;

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

    public function getJobId(): ?string
    {
        return $this->jobId;
    }

    public function setJobId(string $jobId): static
    {
        $this->jobId = $jobId;

        return $this;
    }

    public function getAnonymousVisitorId(): ?string
    {
        return $this->anonymousVisitorId;
    }

    public function setAnonymousVisitorId(string $anonymousVisitorId): static
    {
        $this->anonymousVisitorId = $anonymousVisitorId;

        return $this;
    }

    public function getSiteUrl(): ?string
    {
        return $this->siteUrl;
    }

    public function setSiteUrl(string $siteUrl): static
    {
        $this->siteUrl = $siteUrl;

        return $this;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(int $productId): static
    {
        $this->productId = $productId;

        return $this;
    }

    public function getVariationId(): ?int
    {
        return $this->variationId;
    }

    public function setVariationId(?int $variationId): static
    {
        $this->variationId = $variationId;

        return $this;
    }

    public function getProductTitle(): ?string
    {
        return $this->productTitle;
    }

    public function setProductTitle(string $productTitle): static
    {
        $this->productTitle = $productTitle;

        return $this;
    }

    public function getProductDescription(): ?string
    {
        return $this->productDescription;
    }

    public function setProductDescription(?string $productDescription): static
    {
        $this->productDescription = $productDescription;

        return $this;
    }

    public function getProductImageUrl(): ?string
    {
        return $this->productImageUrl;
    }

    public function setProductImageUrl(?string $productImageUrl): static
    {
        $this->productImageUrl = $productImageUrl;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getProductCategories(): array
    {
        return $this->productCategories;
    }

    /**
     * @param string[] $productCategories
     */
    public function setProductCategories(array $productCategories): static
    {
        $this->productCategories = $productCategories;

        return $this;
    }

    public function isCustomerImageProvided(): bool
    {
        return $this->customerImageProvided;
    }

    public function setCustomerImageProvided(bool $customerImageProvided): static
    {
        $this->customerImageProvided = $customerImageProvided;

        return $this;
    }

    public function getCustomerImagePath(): ?string
    {
        return $this->customerImagePath;
    }

    public function setCustomerImagePath(?string $customerImagePath): static
    {
        $this->customerImagePath = $customerImagePath;

        return $this;
    }

    public function getRequestedAnonymousDailyQuota(): ?int
    {
        return $this->requestedAnonymousDailyQuota;
    }

    public function setRequestedAnonymousDailyQuota(?int $requestedAnonymousDailyQuota): static
    {
        $this->requestedAnonymousDailyQuota = $requestedAnonymousDailyQuota;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreditsConsumed(): int
    {
        return $this->creditsConsumed;
    }

    public function setCreditsConsumed(int $creditsConsumed): static
    {
        $this->creditsConsumed = $creditsConsumed;

        return $this;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function setErrorCode(?string $errorCode): static
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(?string $errorMessage): static
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }

    public function getProviderRequestId(): ?string
    {
        return $this->providerRequestId;
    }

    public function setProviderRequestId(?string $providerRequestId): static
    {
        $this->providerRequestId = $providerRequestId;

        return $this;
    }

    public function getGeneratedImagePath(): ?string
    {
        return $this->generatedImagePath;
    }

    public function setGeneratedImagePath(?string $generatedImagePath): static
    {
        $this->generatedImagePath = $generatedImagePath;

        return $this;
    }

    public function getCompletedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeImmutable $completedAt): static
    {
        $this->completedAt = $completedAt;

        return $this;
    }
}
