<?php

namespace App\Domain\Notification\Event;

use App\Domain\Registry\Model\Proof;
use App\Domain\Registry\Model\Treatment;
use App\Domain\User\Model\Collectivity;
use Symfony\Contracts\EventDispatcher\Event;

class ProofEvent extends Event
{
    /**
     * @var Proof
     * The object that generated the notification
     */
    protected Proof $proof;

    protected string $action;

    public function __construct(Proof $proof, string $action)
    {
        $this->proof = $proof;
        $this->action = $action;
    }

    /**
     * @return Proof
     */
    public function getProof(): Proof
    {
        return $this->proof;
    }

    /**
     * @param Proof $proof
     */
    public function setProof(Proof $proof): void
    {
        $this->proof = $proof;
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