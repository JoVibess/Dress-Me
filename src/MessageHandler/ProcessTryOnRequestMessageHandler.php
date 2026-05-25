<?php

namespace App\MessageHandler;

use App\Entity\TryOnRequest;
use App\Message\ProcessTryOnRequestMessage;
use App\Repository\TryOnRequestRepository;
use App\Service\GeminiTryOnGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ProcessTryOnRequestMessageHandler
{
    public function __construct(
        private TryOnRequestRepository $tryOnRequestRepository,
        private EntityManagerInterface $entityManager,
        private GeminiTryOnGenerator $geminiTryOnGenerator,
    ) {
    }

    public function __invoke(ProcessTryOnRequestMessage $message): void
    {
        $tryOnRequest = $this->tryOnRequestRepository->find($message->tryOnRequestId);

        if (!$tryOnRequest instanceof TryOnRequest) {
            return;
        }

        if (!in_array($tryOnRequest->getStatus(), [TryOnRequest::STATUS_RECEIVED, TryOnRequest::STATUS_FAILED], true)) {
            return;
        }

        $tryOnRequest
            ->setStatus(TryOnRequest::STATUS_PROCESSING)
            ->setErrorCode(null)
            ->setErrorMessage(null)
            ->setCompletedAt(null);
        $this->entityManager->flush();

        try {
            $generation = $this->geminiTryOnGenerator->generate($tryOnRequest);

            $tryOnRequest
                ->setStatus(TryOnRequest::STATUS_COMPLETED)
                ->setProviderRequestId($generation['provider_request_id'])
                ->setGeneratedImagePath($generation['generated_image_path'])
                ->setCreditsConsumed(1)
                ->setCompletedAt(new \DateTimeImmutable());
        } catch (\Throwable $exception) {
            $tryOnRequest
                ->setStatus(TryOnRequest::STATUS_FAILED)
                ->setErrorCode('GENERATION_FAILED')
                ->setErrorMessage($exception->getMessage());
        }

        $this->entityManager->flush();
    }
}
