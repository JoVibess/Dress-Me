<?php

namespace App\Controller\Api;

use App\Dto\Api\WordPressTryOnStatusRequest;
use App\Dto\Api\WordPressValidateKeyRequest;
use App\Dto\Api\WordPressTryOnRequest;
use App\Message\ProcessTryOnRequestMessage;
use App\Entity\TryOnRequest;
use App\Service\ApiTokenValidator;
use App\Service\CreditResolver;
use App\Service\TryOnImageStorage;
use App\Service\TryOnRequestFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Repository\TryOnRequestRepository;

#[Route('/api/wordpress', name: 'api_wordpress_')]
final class WordPressController extends AbstractController
{
    #[Route('/validate-key', name: 'validate_key', methods: ['POST'])]
    public function validateKey(
        Request $request,
        ValidatorInterface $validator,
        ApiTokenValidator $apiTokenValidator,
        CreditResolver $creditResolver,
    ): JsonResponse {
        $payload = json_decode($request->getContent(), true);

        if (!\is_array($payload)) {
            return $this->json([
                'success' => false,
                'error_code' => 'INVALID_JSON',
                'message' => 'Request body must be valid JSON.',
            ], 400);
        }

        $validateKeyRequest = WordPressValidateKeyRequest::fromArray($payload);
        $violations = $validator->validate($validateKeyRequest);

        if (count($violations) > 0) {
            $errors = [];

            foreach ($violations as $violation) {
                $property = $violation->getPropertyPath();
                $errors[$property] = $violation->getMessage();
            }

            return $this->json([
                'success' => false,
                'error_code' => 'INVALID_PAYLOAD',
                'message' => 'Payload validation failed.',
                'errors' => $errors,
            ], 400);
        }

        $apiToken = $apiTokenValidator->findValidStoreToken(
            (string) $validateKeyRequest->apiKey,
            $validateKeyRequest->siteUrl,
        );

        if (null === $apiToken) {
            return $this->json([
                'success' => false,
                'error_code' => 'INVALID_API_KEY',
                'message' => 'The provided API key is invalid for this store.',
            ], 401);
        }

        $store = $apiToken->getStore();
        $user = $store?->getUser();

        if (null === $store || null === $user) {
            return $this->json([
                'success' => false,
                'error_code' => 'STORE_NOT_FOUND',
                'message' => 'No active store is associated with this API key.',
            ], 404);
        }

        return $this->json([
            'success' => true,
            'store_name' => $store->getName(),
            'store_website' => $store->getWebsite(),
            'remaining_credits' => $creditResolver->resolveAvailableCredits($user),
            'anonymous_daily_quota' => $store->getAnonymousDailyQuota(),
        ]);
    }

    #[Route('/try-on/request', name: 'try_on_request', methods: ['POST'])]
    public function requestTryOn(
        Request $request,
        ValidatorInterface $validator,
        ApiTokenValidator $apiTokenValidator,
        CreditResolver $creditResolver,
        TryOnRequestFactory $tryOnRequestFactory,
        TryOnImageStorage $tryOnImageStorage,
        EntityManagerInterface $entityManager,
        MessageBusInterface $messageBus,
    ): JsonResponse {
        $payload = json_decode($request->getContent(), true);

        if (!\is_array($payload)) {
            return $this->json([
                'success' => false,
                'error_code' => 'INVALID_JSON',
                'message' => 'Request body must be valid JSON.',
            ], 400);
        }

        $tryOnPayload = WordPressTryOnRequest::fromArray($payload);
        $violations = $validator->validate($tryOnPayload);

        if (count($violations) > 0) {
            $errors = [];

            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            return $this->json([
                'success' => false,
                'error_code' => 'INVALID_PAYLOAD',
                'message' => 'Payload validation failed.',
                'errors' => $errors,
            ], 400);
        }

        $apiToken = $apiTokenValidator->findValidStoreToken(
            (string) $tryOnPayload->apiKey,
            $tryOnPayload->siteUrl,
        );

        if (null === $apiToken) {
            return $this->json([
                'success' => false,
                'error_code' => 'INVALID_API_KEY',
                'message' => 'The provided API key is invalid for this store.',
            ], 401);
        }

        $store = $apiToken->getStore();
        $user = $store?->getUser();

        if (null === $store || null === $user) {
            return $this->json([
                'success' => false,
                'error_code' => 'STORE_NOT_FOUND',
                'message' => 'No active store is associated with this API key.',
            ], 404);
        }

        $remainingCredits = $creditResolver->resolveAvailableCredits($user);

        if ($remainingCredits < 1) {
            return $this->json([
                'success' => false,
                'error_code' => 'INSUFFICIENT_CREDITS',
                'message' => 'Not enough credits.',
                'remaining_credits' => $remainingCredits,
            ], 402);
        }

        $tryOnRequest = $tryOnRequestFactory->createFromWordPressPayload($store, $tryOnPayload);

        if (null !== $tryOnPayload->customerImage) {
            try {
                $tryOnRequest->setCustomerImagePath(
                    $tryOnImageStorage->storeCustomerImage(
                        (string) $tryOnRequest->getJobId(),
                        $tryOnPayload->customerImage,
                    ),
                );
            } catch (\RuntimeException $exception) {
                return $this->json([
                    'success' => false,
                    'error_code' => 'INVALID_CUSTOMER_IMAGE',
                    'message' => $exception->getMessage(),
                ], 400);
            }
        }

        $entityManager->persist($tryOnRequest);
        $entityManager->flush();
        $messageBus->dispatch(new ProcessTryOnRequestMessage((int) $tryOnRequest->getId()));

        return $this->json([
            'success' => true,
            'job_id' => $tryOnRequest->getJobId(),
            'status' => TryOnRequest::STATUS_RECEIVED,
            'remaining_credits' => $remainingCredits,
            'message' => 'Try-on request received successfully.',
        ], 202);
    }

    #[Route('/try-on/status', name: 'try_on_status', methods: ['POST'])]
    public function tryOnStatus(
        Request $request,
        ValidatorInterface $validator,
        ApiTokenValidator $apiTokenValidator,
        TryOnRequestRepository $tryOnRequestRepository,
    ): JsonResponse {
        $payload = json_decode($request->getContent(), true);

        if (!\is_array($payload)) {
            return $this->json([
                'success' => false,
                'error_code' => 'INVALID_JSON',
                'message' => 'Request body must be valid JSON.',
            ], 400);
        }

        $statusRequest = WordPressTryOnStatusRequest::fromArray($payload);
        $violations = $validator->validate($statusRequest);

        if (count($violations) > 0) {
            $errors = [];

            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            return $this->json([
                'success' => false,
                'error_code' => 'INVALID_PAYLOAD',
                'message' => 'Payload validation failed.',
                'errors' => $errors,
            ], 400);
        }

        $apiToken = $apiTokenValidator->findValidStoreToken(
            (string) $statusRequest->apiKey,
            $statusRequest->siteUrl,
        );

        if (null === $apiToken || null === $apiToken->getStore()) {
            return $this->json([
                'success' => false,
                'error_code' => 'INVALID_API_KEY',
                'message' => 'The provided API key is invalid for this store.',
            ], 401);
        }

        $tryOnRequest = $tryOnRequestRepository->findOneByJobIdAndStore(
            (string) $statusRequest->jobId,
            (int) $apiToken->getStore()->getId(),
        );

        if (!$tryOnRequest instanceof TryOnRequest) {
            return $this->json([
                'success' => false,
                'error_code' => 'TRY_ON_REQUEST_NOT_FOUND',
                'message' => 'No try-on request matches this job id for the current store.',
            ], 404);
        }

        return $this->json([
            'success' => true,
            'job_id' => $tryOnRequest->getJobId(),
            'status' => $tryOnRequest->getStatus(),
            'generated_image_url' => $this->buildPublicAssetUrl($request, $tryOnRequest->getGeneratedImagePath()),
            'error_code' => $tryOnRequest->getErrorCode(),
            'error_message' => $tryOnRequest->getErrorMessage(),
        ]);
    }

    private function buildPublicAssetUrl(Request $request, ?string $relativePath): ?string
    {
        if (null === $relativePath || '' === trim($relativePath)) {
            return null;
        }

        return sprintf('%s/%s', $request->getSchemeAndHttpHost(), ltrim($relativePath, '/'));
    }
}
