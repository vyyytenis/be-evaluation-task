<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Notification;
use App\NotificationPublisher\Infrastructure\Persistence\DoctrineNotificationRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class NotificationControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private DoctrineNotificationRepository&MockObject $doctrineNotificationRepository;
    private MessageBusInterface&MockObject $messageBus;

    public function testSomething(): void
    {
        $data = [
            "notifications" => [
                [
                    "userId" => 1,
                    "channel" => "email",
                    "content" => "email to fail",
                    "receiver" => "somenew@gmail.com"
                ]
            ]
        ];

//        $this->messageBus
//            ->expects($this->once())
//            ->method('dispatch')
//            ->willReturn(new Envelope(new \stdClass()))
//        ;

        $body = json_encode($data);
        $this->client->request('POST', '/api/send-notifications', [], [], [], $body);
        $this->assertResponseIsSuccessful();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
//        $container = $this->client->getContainer();
//
//        $this->messageBus = $this->createMock(MessageBusInterface::class);
//        $container->set(MessageBusInterface::class, $this->messageBus);
    }
}
