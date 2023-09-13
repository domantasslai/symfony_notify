<?php

namespace App\Observer;

use App\Service\Notification;


interface NotificationInterface
{
    public function send(Notification $notification): bool;

    public function getRecipient(): string;

    public function getContent(): string;
}
