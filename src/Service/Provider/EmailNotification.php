<?php

namespace App\Service\Provider;

use App\Exception\NotificationNotSentException;
use App\Observer\NotificationInterface;
use App\Service\Notification;

use Aws\Ses\SesClient;
use Aws\Ses\Exception\SesException;


class EmailNotification implements NotificationInterface
{
    private string $recipient;
    private string $content;

    public function __construct(
        private string $awsRegion, private string $awsAccessKey, private string $awsSecretKey
    )
    {
    }

    public function getRecipient(): string
    {
        return $this->recipient;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Send an email notification using Amazon SES.
     *
     * This method sends an email notification using Amazon SES by setting the recipient's email address and the notification content.
     *
     * @param Notification $notification The notification to be sent.
     * @return bool True if the email notification was successfully sent, false otherwise.
     * @throws NotificationNotSentException If there is an issue sending the email notification.
     */
    public function send(Notification $notification): bool
    {
        $this->recipient = $notification->getRecipient()->getEmail();
        $this->content = $notification->getContent();

        try {
            $sesClient = new SesClient([
                'region' => $this->awsRegion,
                'credentials' => [
                    'key' => $this->awsAccessKey,
                    'secret' => $this->awsSecretKey,
                ],
            ]);

            $sourceEmail = ''; // fill with valid email

            // Send the email notification using Amazon SES.
            $sesClient->sendEmail([
                'Source' => $sourceEmail,
                'Destination' => [
                    'ToAddresses' => [$this->recipient],
                ],
                'Message' => [
                    'Subject' => [
                        'Data' => 'Notification',
                    ],
                    'Body' => [
                        'Text' => [
                            'Data' => $this->content,
                        ],
                    ],
                ],
            ]);

            return true;

        } catch (SesException| \Exception $e) {
            // Handle exceptions if necessary (e.g., logging why notification was not sent) and throw a NotificationNotSentException.
            throw new NotificationNotSentException('Email notification failed: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
