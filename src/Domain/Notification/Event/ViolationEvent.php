<?php

namespace App\Domain\Notification\Event;

use App\Domain\Registry\Model\Violation;
use Symfony\Contracts\EventDispatcher\Event;

class ViolationEvent extends Event
{
    /**
     * @var Violation
     * The object that generated the notification
     */
    protected Violation $violation;

    protected string $action;

    public function __construct(Violation $violation, string $action)
    {
        $this->violation = $violation;
        $this->action = $action;
    }

    /**
     * @return Violation
     */
    public function getViolation(): Violation
    {
        return $this->violation;
    }

    /**
     * @param Violation $violation
     */
    public function setViolation(Violation $violation): void
    {
        $this->violation = $violation;
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