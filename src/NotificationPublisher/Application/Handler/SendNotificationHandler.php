<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\Handler;

use App\NotificationPublisher\Application\Command\SendNotificationMessage;
use App\NotificationPublisher\Domain\Model\NotificationMessage;
use App\NotificationPublisher\Domain\Service\NotificationManager;
use App\NotificationPublisher\Domain\Repository\NotificationRepositoryInterface;
use App\Entity\Notification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\RateLimiter\RateLimiterFactory;

#[AsMessageHandler]
class SendNotificationHandler
{
    public function __construct(
        private NotificationManager $notificationManager,
        private NotificationRepositoryInterface $repository,
        private RateLimiterFactory $notificationUserLimiter
    ) {
    }

    public function __invoke(SendNotificationMessage $sendNotificationMessage): void
    {
        /** @var Notification|null $entity */
        $entity = $this->repository->find($sendNotificationMessage->getNotificationId());
        $limiter = $this->notificationUserLimiter->create((string)$entity->getUserId());
        $limit = $limiter->consume();

        if (!$limit->isAccepted()) {
            $entity
                ->setStatus(Notification::STATUS_THROTTLED)
                ->setErrorMessage(
                    sprintf(
                        "User %d exceeded rate limit for channel %s",
                        $entity->getUserId(),
                        $entity->getChannel()
                    )
                )
            ;
            $this->repository->save($entity);

            return;
        }

        $message = new NotificationMessage(
            $entity->getUserId(),
            $entity->getChannel(),
            $entity->getContent(),
            $entity->getReceiver()
        );

        try {
            $success = $this->notificationManager->send($message);
        } catch (\RuntimeException $e) {
            $entity
                ->setStatus(Notification::STATUS_FAILED)
                ->setErrorMessage($e->getMessage())
            ;
            $this->repository->save($entity);

            return;
        }
        ;

        if ($success) {
            $entity->setStatus(Notification::STATUS_SENT);
            $entity->setSentAt(new \DateTimeImmutable());
        } else {
            $entity->setStatus(Notification::STATUS_FAILED);
            $entity->setErrorMessage('Failed to send notification');
        }

        $this->repository->save($entity);
    }
}
