<?php

namespace App\Domain\Notification\Event;

use App\Domain\Registry\Model\Mesurement;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\EventDispatcher\Event;

class SendEmailNotificationEvent extends Event
{
    /**
     * @var Email
     *
     */
    protected Email $email;

    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @param Email $email
     */
    public function setEmail(Email $email): void
    {
        $this->email = $email;
    }

}
