<?php

namespace App\NotificationPublisher\Infrastructure\Provider\Sms;

use App\NotificationPublisher\Domain\Model\NotificationMessage;
use App\NotificationPublisher\Infrastructure\Provider\ProviderInterface;
use App\NotificationPublisher\Infrastructure\Provider\ProviderException;
use Twilio\Rest\Client;

class TwilioSmsProvider implements ProviderInterface
{
    public function __construct(
        private string $accSid,
        private string $authToken,
        private string $fromNumber
    ) {}

    public function send(NotificationMessage $message): bool
    {
        try {
            $twilio = new Client($this->accSid, $this->authToken);

            $twilioResponse  = $twilio->messages->create(
                $message->receiver,
                [
                    "body" =>$message->content,
                    "from" => $this->fromNumber,
                ]
            );

            dump("Twillio RES");
            dump($twilioResponse);

            dump(sprintf(
                "Sending SMS to user %d: %s Sms to %s Twillio res %",
                $message->userId,
                $message->content,
                $message->receiver,
            ));

            return true;
        } catch (\Throwable $e) {
            throw new ProviderException("Twilio SMS sending failed: " . $e->getMessage(), 0, $e);
        }
    }

    public function getChannel(): string
    {
        return 'sms';
    }
}
