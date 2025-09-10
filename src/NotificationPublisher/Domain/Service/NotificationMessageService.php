<?php

namespace App\NotificationPublisher\Domain\Service;

use App\Entity\Notification;
use App\NotificationPublisher\Application\Command\SendNotificationMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class NotificationMessageService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $bus
    ) {
    }

    public function createAndSave(array $notifications): void
    {
        $createdNotifications = [];

        foreach ($notifications as $notificationData) {
            $notification = (new Notification())
                ->setUserId($notificationData['userId'])
                ->setChannel($notificationData['channel'])
                ->setContent($notificationData['content'])
                ->setReceiver($notificationData['receiver'])
                ->setCreatedAt(new \DateTimeImmutable())
                ->setStatus(Notification::STATUS_PENDING);

            $this->entityManager->persist($notification);
            $createdNotifications[] = $notification;
        }

        $this->entityManager->flush();

        foreach ($createdNotifications as $notification) {
            $this->bus->dispatch(new SendNotificationMessage($notification->getId()));
        }
    }
}
