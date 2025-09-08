<?php

namespace App\NotificationPublisher\UserInterface\Controller;

use App\NotificationPublisher\Domain\Repository\NotificationRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\NotificationPublisher\Application\Command\SendNotificationCommand;

class NotificationController extends AbstractController
{
    public function __construct(
        private MessageBusInterface $bus,
        private NotificationRepositoryInterface $repository,
//        private ValidatorInterface $validator,
    ) {
    }

    #[Route('/api/send-notifications', name: 'send_notifications', methods: ['POST'])]
    public function send(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        // validatoriaus check
        if (!isset($data['notifications']) || !is_array($data['notifications'])) {
            return new JsonResponse(['error' => 'Invalid payload'], 400);
        }

        //data keliauja i service

        foreach ($data['notifications'] as $notificationData) {
            $this->bus->dispatch(new SendNotificationCommand(
                $notificationData['userId'],
                $notificationData['channel'],
                $notificationData['content'],
                $notificationData['receiver']
            ));
        }

        return new JsonResponse([
            'status' => 'queued',
            'count' => count($data['notifications'])
        ], 202);
    }

    #[Route('/api/list-notifications', name: 'list_notifications', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $notifications = $this->repository->findAll();

        $data = array_map(function ($notification) {
            return [
                'id' => $notification->getId(),
                'userId' => $notification->getUserId(),
                'channel' => $notification->getChannel(),
                'content' => $notification->getContent(),
                'status' => $notification->getStatus(),
                'receiver' => $notification->getReceiver(),
                'createdAt' => $notification->getCreatedAt()->format('Y-m-d H:i:s'),
                'sentAt' => $notification->getSentAt()?->format('Y-m-d H:i:s'),
                'errorMessage' => $notification->getErrorMessage(),
            ];
        }, $notifications);

        return new JsonResponse($data, 200);
    }
}
