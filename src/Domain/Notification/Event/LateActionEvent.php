<?php

namespace App\Domain\Notification\Event;

use App\Domain\Registry\Model\Mesurement;
use App\Domain\Registry\Model\Violation;
use Symfony\Contracts\EventDispatcher\Event;

class LateActionEvent extends Event
{
    /**
     * @var Mesurement
     * The object that generated the notification
     */
    protected Mesurement $mesurement;

    public function __construct(Mesurement $mesurement)
    {
        $this->mesurement = $mesurement;
    }

    /**
     * @return Mesurement
     */
    public function getMesurement(): Mesurement
    {
        return $this->mesurement;
    }

    /**
     * @param Mesurement $mesurement
     */
    public function setMesurement(Mesurement $mesurement): void
    {
        $this->mesurement = $mesurement;
    }
}