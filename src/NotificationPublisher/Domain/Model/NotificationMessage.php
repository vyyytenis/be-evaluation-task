<?php

namespace App\NotificationPublisher\Domain\Model;

class NotificationMessage
{
    public function __construct(
        public readonly int $userId,
        public readonly string $channel,
        public readonly string $content,
        public readonly string $receiver
    ) {}
}
