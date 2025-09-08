<?php

namespace App\NotificationPublisher\Domain\Repository;

use App\Entity\Notification;

interface NotificationRepositoryInterface
{
    /**
     * @return Notification[]
     */
    public function findAll(): array;
    public function save(Notification $notification): void;

    public function findPending(): array;
}
