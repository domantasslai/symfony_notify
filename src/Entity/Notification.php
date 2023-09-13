<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $chanel = null;

    #[ORM\Column(length: 255)]
    private ?string $chanelExecutor = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $sendingTo = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChanel(): ?string
    {
        return $this->chanel;
    }

    public function setChanel(string $chanel): static
    {
        $this->chanel = $chanel;

        return $this;
    }

    public function getChanelExecutor(): ?string
    {
        return $this->chanelExecutor;
    }

    public function setChanelExecutor(string $chanelExecutor): static
    {
        $this->chanelExecutor = $chanelExecutor;

        return $this;
    }

    public function getSendingTo(): ?string
    {
        return $this->sendingTo;
    }

    public function setSendingTo(string $sendingTo): static
    {
        $this->sendingTo = $sendingTo;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }
}
