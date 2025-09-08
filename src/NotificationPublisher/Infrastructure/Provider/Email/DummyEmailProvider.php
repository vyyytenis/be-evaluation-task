<?php

namespace App\NotificationPublisher\Infrastructure\Provider\Email;

use App\NotificationPublisher\Domain\Model\NotificationMessage;
use App\NotificationPublisher\Infrastructure\Provider\ProviderInterface;
use App\NotificationPublisher\Infrastructure\Provider\ProviderException;

class DummyEmailProvider implements ProviderInterface
{
    public function send(NotificationMessage $message): bool
    {
        // Simulate sending email
        dump(sprintf(
            "DummyEmailProvider: Simulating sending email to %s with content: %s",
            $message->receiver,
            $message->content
        ));

        // Simulate a random failure for failover testing
        if (rand(0, 1) === 0) {
            throw new ProviderException("DummyEmailProvider failed randomly!");
        }

        return true;
    }

    public function getChannel(): string
    {
        return 'email';
    }
}
