<?php

namespace App\NotificationPublisher\Infrastructure\Provider\Email;

use App\NotificationPublisher\Domain\Model\NotificationMessage;
use App\NotificationPublisher\Infrastructure\Provider\ProviderInterface;
use App\NotificationPublisher\Infrastructure\Provider\ProviderException;
use Aws\Ses\SesClient;
use Aws\Exception\AwsException;


class AwsSesEmailProvider implements ProviderInterface
{
    private SesClient $sesClient;
    public function __construct(
        private string $apiKey,
        private string $apiSecret,
        private string $fromEmail,
        private string $region
    ) {}

    public function send(NotificationMessage $message): bool
    {
        try {
            $this->sesClient = new SesClient([
                'version' => 'latest',
                'region'  => $this->region,
                'credentials' => [
                    'key'    => $this->apiKey,
                    'secret' => $this->apiSecret,
                ],
            ]);

            $this->sesClient->sendEmail([
                'Destination' => [
                    'ToAddresses' => [$message->receiver],
                ],
                'Message' => [
                    'Body' => [
                        'Text' => [
                            'Charset' => 'UTF-8',
                            'Data' => $message->content,
                        ],
                    ],
                    'Subject' => [
                        'Charset' => 'UTF-8',
                        'Data' => $message->content ?? 'Notification',
                    ],
                ],
                'Source' => $this->fromEmail,
            ]);

            // Mock sending email through AWS SES
            dump(sprintf(
                "Sending Email to user %d: %s Email to %s",
                $message->userId,
                $message->content,
                $message->receiver
            ));

            return true;
        } catch (AwsException  $e) {
            dump($e->getMessage());
            throw new ProviderException("AWS SES sending failed: " . $e->getAwsErrorMessage(), 0, $e);
        }
    }

    public function getChannel(): string
    {
        return 'email';
    }
}
