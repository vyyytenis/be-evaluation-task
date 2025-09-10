<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\Message;

class NotificationMessage
{
    public function __construct(
        public string $userId,
        public string $channel,
        public string $content,
        public string $receiver,
    ) {
    }
}
