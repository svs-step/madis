<?php

namespace App\Domain\User\Model;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class LoginAttempt
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var string|null
     */
    private $ip;

    /**
     * @var string|null
     */
    private $email;

    /**
     * @var int|null
     */
    private $attempts;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function __toString()
    {
        return $this->ip . ' - ' . $this->attempts;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): void
    {
        $this->ip = $ip;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getAttempts(): ?int
    {
        return $this->attempts;
    }

    public function setAttempts(?int $attempts): void
    {
        $this->attempts = $attempts;
    }
}
