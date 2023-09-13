<?php

namespace App\Message;

use App\Event\NotificationRetryEvent;
use App\Service\Notification;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class NotificationRetryEventHandler implements MessageHandlerInterface
{
    public function __construct(private Notification $notification)
    {
    }

    /**
     * Invoke method for handling notification retry events.
     *
     * This method is invoked when a `NotificationRetryEvent` is dispatched. It retrieves the user and content from the event
     * and attempts to resend the notification using the provided information.
     *
     * @param NotificationRetryEvent $event The retry event containing user and content details.
     *
     * @throws \InvalidArgumentException If there is an issue with resending the notification, this exception may be thrown.
     */
    public function __invoke(NotificationRetryEvent $event)
    {
        // Set the recipient and content of the notification based on the event data.
        $this->notification->setRecipient($event->getUser());
        $this->notification->setContent($event->getContent());

        try {
            // Attempt to resend the notification.
            $this->notification->sendNotification($this->notification);
        } catch (\InvalidArgumentException $e) {
            // Handle exceptions if necessary (e.g., logging why notification was not sent).
        }
    }
}
