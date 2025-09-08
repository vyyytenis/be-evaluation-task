<?php

namespace App\NotificationPublisher\Infrastructure\Provider;

use App\NotificationPublisher\Domain\Model\NotificationMessage;

interface ProviderInterface
{
    /**
     * Send a notification message.
     *
     * @throws ProviderException
     */
    public function send(NotificationMessage $message): bool;

    /**
     * Return the channel this provider supports (e.g., sms, email, push).
     */
    public function getChannel(): string;
}
