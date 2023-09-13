<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class NotificationRetryEvent extends Event
{
    public function __construct(private object $user, private string $content)
    {

    }

    public function getUser(): object
    {
        return $this->user;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
