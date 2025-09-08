<?php

namespace App\NotificationPublisher\Domain\Service;

use App\NotificationPublisher\Domain\Model\NotificationMessage;
use App\NotificationPublisher\Infrastructure\Provider\ProviderInterface;
use App\NotificationPublisher\Infrastructure\Provider\ProviderException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class NotificationManager
{
    /**
     * @param array<string, ProviderInterface[]> $providersByChannel
     * @param array<string, array{enabled: bool}> $config
     */
    public function __construct(
        private array $providersByChannel,
        private ParameterBagInterface $params
    ) {}

    public function send(NotificationMessage $message): bool
    {
        $enabledChannels = $this->params->get('notifications')['channels'];

        if (!($enabledChannels[$message->channel]['enabled'] ?? false)) {
            throw new \RuntimeException("Channel {$message->channel} is disabled via configuration.");
        }

        $providers = $this->providersByChannel[$message->channel] ?? [];

        if (empty($providers)) {
            throw new \RuntimeException("No providers available for channel: {$message->channel}");
        }

        foreach ($providers as $provider) {
            try {
                return $provider->send($message);
            } catch (ProviderException $e) {
                continue;
            }
        }

        return false;
    }
}
