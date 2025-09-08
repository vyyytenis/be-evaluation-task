<?php

namespace App\NotificationPublisher\Infrastructure\Persistence;

use App\Entity\Notification;
use App\NotificationPublisher\Domain\Repository\NotificationRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineNotificationRepository implements NotificationRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function findAll(): array
    {
        return $this->em->getRepository(Notification::class)->findAll();
    }

    public function save(Notification $notification): void
    {
        $this->em->persist($notification);
        $this->em->flush();
    }

    public function findPending(): array
    {
        return $this->em->getRepository(Notification::class)
            ->findBy(['status' => 'pending']);
    }
}
