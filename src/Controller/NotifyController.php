<?php

namespace App\Controller;

use App\Exception\WrongInputException;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

use App\Service\Notification;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NotifyController extends AbstractController
{
    private Notification $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException|WrongInputException
     */
    #[Route('/send', name: 'send', methods: ['GET'])]
    public function send(EntityManagerInterface $entityManager, UserRepository $userRepository, NotificationRepository $notificationRepository): Response
    {
        $user = $userRepository->findByEmailOrCreate(
            'domantasslai@gmail.com',
            ['phone_number' => '+37067924162'],
            $entityManager
        );

        if ($notificationRepository->countNotificationsInsertedOneHourAgo($user) > 300) {
            return $this->json('To many attempts, please try again in one hour', 500);
        }

        $this->notification->setRecipient($user);
        $this->notification->setContent('Hello, this is your notification content.');

        $success = $this->notification->sendNotification($this->notification);

        if ($success) {
            return $this->json('Notification sent successfully');
        }

        return $this->json('Notification was not sent', 500);
    }
}
