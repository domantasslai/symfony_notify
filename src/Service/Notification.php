<?php

namespace App\Service;

use App\Event\NotificationRetryEvent;
use App\Exception\NotificationNotExistsException;
use App\Exception\NotificationNotSentException;
use App\Provider\NotificationServiceProvider;

use App\Repository\NotificationRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class Notification
{
    private object $recipient;
    private string $content;

    public function __construct(
        private readonly NotificationServiceProvider $serviceProvider,
        private MessageBusInterface                  $messageBus,
        private NotificationRepository               $notificationRepository
    )
    {
    }

    public function getRecipient(): object
    {
        return $this->recipient;
    }

    public function setRecipient(object $recipient): void
    {
        $this->recipient = $recipient;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * Send a notification using available notification providers.
     *
     * This method attempts to send a notification using various notification providers based on the channels available.
     * It iterates through the available channels and their providers, attempting to send the notification through each one until success or all options are exhausted.
     *
     * @param self $notification The notification to be sent.
     *
     * @return bool True if the notification was successfully sent through at least one provider, false otherwise.
     */
    public function sendNotification(self $notification): bool
    {
        // get all available notification sending providers with their channel name
        $channelsToUse = $this->serviceProvider->getChannels();

        foreach ($channelsToUse as $channel) {
            try {
                $providers = $this->serviceProvider->getProvidersByChannel($channel);
                foreach ($providers as $provider) {

                    // Attempt to send the notification using the current provider.
                    $sent = $provider->send($notification);

                    if ($sent) {
                        // If the notification was successfully sent, save notification record.
                        $this->notificationRepository->create([
                            'user' => $notification->getRecipient(),
                            'to' => $provider->getRecipient(),
                            'chanel' => $channel,
                            'chanelExecutor' => get_class($provider),
                            'message' => $provider->getContent()
                        ]);

                        return true; // Notification sent successfully.
                    }

                }
            } catch (\Exception|NotificationNotSentException|NotificationNotExistsException $e) {
                // Handle exceptions if necessary (e.g., logging why notification was not sent).
            }
        }

        // If all providers and channels fail, schedule a delayed retry
        $this->handleRetry($notification);

        return false; // Notification not sent through any provider.
    }

    /**
     * Handle Retry for a Notification.
     *
     * This method is responsible for handling retries for notifications.
     * If all notification providers fail to deliver a notification, this method is called to initiate a retry.
     * It dispatches a NotificationRetryEvent event, which is processed asynchronously by the NotificationRetryEventHandler class.
     * please run 'bin/console messenger:consume async' in the terminal for dispatching event asynchronously
     *
     * @param self $notification The notification to be retried.
     *
     */
    public function handleRetry(self $notification): void
    {
        // Create a new NotificationRetryEvent using the recipient and content of the notification.
        $event = new NotificationRetryEvent($notification->getRecipient(), $notification->getContent());

        // Set a delay for retrying the event. In this case, it's set to 3,600,000 milliseconds (1 hour).
        $delay = 3600000;

        try {
            $this->messageBus->dispatch($event, [new DelayStamp($delay)]);
        } catch (\Symfony\Component\Messenger\Exception\TransportException $e) {
            // Handle exceptions if necessary (e.g., logging why event was not dispatched).
        }
    }
}
