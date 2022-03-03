<?php

namespace App\Domain\Notification\Event;

use App\Domain\Registry\Model\Contractor;
use Symfony\Contracts\EventDispatcher\Event;

class ContractorEvent extends Event
{
    /**
     * @var Contractor
     * The object that generated the notification
     */
    protected Contractor $contractor;

    protected string $action;

    public function __construct(Contractor $contractor, string $action)
    {
        $this->contractor = $contractor;
        $this->action = $action;
    }

    /**
     * @return Contractor
     */
    public function getContractor(): Contractor
    {
        return $this->contractor;
    }

    /**
     * @param Contractor $contractor
     */
    public function setContractor(Contractor $contractor): void
    {
        $this->contractor = $contractor;
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