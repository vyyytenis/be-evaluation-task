<?php

namespace App\NotificationPublisher\Application\Handler;

use App\NotificationPublisher\Application\Command\SendNotificationCommand;
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
    ) {}

    public function __invoke(SendNotificationCommand $command): void
    {
        $limiter = $this->notificationUserLimiter->create((string)$command->userId);
        $limit = $limiter->consume();

        $notification = new Notification();
        $notification->setUserId($command->userId);
        $notification->setChannel($command->channel);
        $notification->setContent($command->content);
        $notification->setReceiver($command->receiver);
        $notification->setCreatedAt(new \DateTimeImmutable());

        if (!$limit->isAccepted()) {
            $notification->setStatus('throttled');
            $notification->setErrorMessage(
                sprintf("User %d exceeded rate limit for channel %s", $command->userId, $command->channel)
            );
            $this->repository->save($notification);
            return;
        }

        $message = new NotificationMessage(
            $command->userId,
            $command->channel,
            $command->content,
            $command->receiver
        );

        try {
            $success = $this->notificationManager->send($message);
        } catch (\RuntimeException $e) {
            $notification->setStatus('failed');
            $notification->setErrorMessage($e->getMessage());
            $this->repository->save($notification);
            return;
        }

        if ($success) {
            $notification->setStatus('sent');
            $notification->setSentAt(new \DateTimeImmutable());
        } else {
            $notification->setStatus('failed');
            $notification->setErrorMessage('Failed to send notification');
        }

        $this->repository->save($notification);
    }
}
