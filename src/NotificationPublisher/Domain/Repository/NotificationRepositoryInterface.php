<?php

namespace App\NotificationPublisher\Domain\Repository;

use App\Entity\Notification;

interface NotificationRepositoryInterface
{
    public function find(int $id): ?Notification;
    /**
     * @return Notification[]
     */
    public function findAll(): array;
    public function save(Notification $notification): void;
    /**
     * @return Notification[]
     */
    public function findPending(): array;
}
