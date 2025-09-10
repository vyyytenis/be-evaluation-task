<?php

namespace App\NotificationPublisher\UserInterface\Controller;

use App\Dto\RequestDto;
use App\NotificationPublisher\Domain\Repository\NotificationRepositoryInterface;
use App\NotificationPublisher\Domain\Service\NotificationMessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\NotificationPublisher\Application\Command\SendNotificationMessage;

class NotificationController extends AbstractController
{
    public function __construct(
        private NotificationRepositoryInterface $repository,
        private NotificationMessageService $messageService,
    ) {
    }

    #[Route('/api/send-notifications', name: 'send_notifications', methods: ['POST'])]
    public function send(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['notifications']) || !is_array($data['notifications'])) {
            return new JsonResponse(['error' => 'Invalid payload'], 400);
        }

        $this->messageService->createAndSave($data['notifications']);

        return new JsonResponse([
            'status' => 'queued',
            'count' => count($data['notifications'])
        ], 201);
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
