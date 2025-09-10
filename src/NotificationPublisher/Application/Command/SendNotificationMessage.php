<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\Command;

class SendNotificationMessage
{
    public function __construct(
        private int    $notificationId,
    ) {
    }

    public function getNotificationId(): int
    {
        return $this->notificationId;
    }
}
