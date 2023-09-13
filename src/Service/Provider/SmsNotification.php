<?php

namespace App\Service\Provider;

use App\Exception\NotificationNotSentException;
use App\Observer\NotificationInterface;
use App\Service\Notification;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Rest\Client;

class SmsNotification implements NotificationInterface
{
    private string $recipient;
    private string $content;

    public function __construct(private string $twilioSid, private string $twilioToken, private string $twilioPhoneNumber)
    {}

    public function getRecipient(): string
    {
        return $this->recipient;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Send an SMS notification.
     *
     * This method sends an SMS notification using Twilio by setting the recipient's phone number and the notification content.
     *
     * @param Notification $notification The notification to be sent.
     * @return bool True if the notification was successfully sent, false otherwise.
     * @throws NotificationNotSentException If there is an issue sending the SMS notification.
     */
    public function send(Notification $notification): bool
    {
        $this->recipient = $notification->getRecipient()->getPhoneNumber();
        $this->content = $notification->getContent();

        try {
            // Create a Twilio client and send the SMS message.
            $client = new Client($this->twilioSid, $this->twilioToken);

            $message = $client->messages->create(
                $this->recipient,
                [
                    'from' => $this->twilioPhoneNumber,
                    'body' => $this->content
                ]
            );

            return $message->sid !== null;

        } catch (ConfigurationException|\Exception $e) {
            // Handle exceptions if necessary (e.g., logging why notification was not sent) and throw a NotificationNotSentException.
            throw new NotificationNotSentException('SMS notification failed: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
