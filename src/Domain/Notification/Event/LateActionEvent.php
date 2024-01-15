<?php

namespace App\Domain\Notification\Event;

use App\Domain\Registry\Model\Mesurement;
use Symfony\Contracts\EventDispatcher\Event;

class LateActionEvent extends Event
{
    protected Mesurement $mesurement;

    public function __construct(Mesurement $mesurement)
    {
        $this->mesurement = $mesurement;
    }

    public function getMesurement(): Mesurement
    {
        return $this->mesurement;
    }

    public function setMesurement(Mesurement $mesurement): void
    {
        $this->mesurement = $mesurement;
    }
}
