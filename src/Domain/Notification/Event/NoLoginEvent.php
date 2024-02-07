<?php

namespace App\Domain\Notification\Event;

use App\Domain\User\Model\User;
use Symfony\Contracts\EventDispatcher\Event;

class NoLoginEvent extends Event
{
    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}
