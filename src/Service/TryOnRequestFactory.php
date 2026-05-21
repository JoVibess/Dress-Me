<?php

namespace App\Service;

use App\Dto\Api\WordPressTryOnRequest;
use App\Entity\Store;
use App\Entity\TryOnRequest;

final class TryOnRequestFactory
{
    public function createFromWordPressPayload(Store $store, WordPressTryOnRequest $payload): TryOnRequest
    {
        $jobId = sprintf('tryon_%s', bin2hex(random_bytes(8)));
        $product = $payload->product;

        $tryOnRequest = (new TryOnRequest())
            ->setStore($store)
            ->setJobId($jobId)
            ->setAnonymousVisitorId((string) $payload->anonymousVisitorId)
            ->setSiteUrl((string) $payload->siteUrl)
            ->setRequestedAnonymousDailyQuota($payload->anonymousDailyQuota)
            ->setCustomerImageProvided(null !== $payload->customerImage)
            ->setStatus(TryOnRequest::STATUS_RECEIVED)
            ->setCreditsConsumed(0)
            ->setProductId($product?->id ?? 0)
            ->setVariationId($product?->variationId)
            ->setProductTitle((string) $product?->title)
            ->setProductDescription($product?->description)
            ->setProductImageUrl($product?->imageUrl)
            ->setProductCategories($product?->categories ?? []);

        return $tryOnRequest;
    }
}
