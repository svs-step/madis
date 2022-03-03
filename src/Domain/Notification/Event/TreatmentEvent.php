<?php

namespace App\Domain\Notification\Event;

use App\Domain\Registry\Model\Treatment;
use App\Domain\User\Model\Collectivity;
use Symfony\Contracts\EventDispatcher\Event;

class TreatmentEvent extends Event
{
    /**
     * @var Treatment
     * The object that generated the notification
     */
    protected Treatment $treatment;

    protected string $action;

    public function __construct(Treatment $treatment, string $action)
    {
        $this->treatment = $treatment;
        $this->action = $action;
    }

    /**
     * @return Treatment
     */
    public function getTreatment(): Treatment
    {
        return $this->treatment;
    }

    /**
     * @param Treatment $treatment
     */
    public function setTreatment(Treatment $treatment): void
    {
        $this->treatment = $treatment;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
    }


}