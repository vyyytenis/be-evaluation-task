<?php

namespace App\Tests\Functional;

use App\NotificationPublisher\Application\Command\SendNotificationMessage;
use App\NotificationPublisher\Application\Handler\SendNotificationHandler;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class SendNotificationHanlderTest extends KernelTestCase
{
    private SendNotificationHandler $sendNotificationHandler;
    private RateLimiterFactory $rateLimiterFactory;

    public function testSomething(): void
    {
        $message = new SendNotificationMessage(1, 'channel', 'content', 'receiver');
        ($this->sendNotificationHandler)($message);
    }

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $kernel = self::bootKernel();

        /** @var SendNotificationHandler $handler */
        $handler = $kernel->getContainer()->get('test.service_container')->get(SendNotificationHandler::class);
        $this->sendNotificationHandler = $handler;
    }
}
