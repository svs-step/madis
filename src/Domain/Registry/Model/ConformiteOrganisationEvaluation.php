<?php

namespace App\Domain\Registry\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ConformiteOrganisationEvaluation
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var iterable
     */
    private $questions;

    /**
     * @var \DateTime|null
     */
    private $date;

    /**
     * @var iterable
     */
    private $participants;

    /**
     * @var iterable
     */
    private $actionProtections;

    /**
     * Determine if the evaluation is complete, or if it's only a draft.
     *
     * @var bool
     */
    private $complete;

    public function __construct()
    {
        $this->id                = Uuid::uuid4();
        $this->participants      = new ArrayCollection();
        $this->actionProtections = [];
        $this->complete          = false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getQuestions(): iterable
    {
        return $this->questions;
    }

    public function setQuestions(iterable $questions): void
    {
        $this->questions = $questions;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(?\DateTime $date): void
    {
        $this->date = $date;
    }

    public function getParticipants(): iterable
    {
        return $this->participants;
    }

    public function setParticipants(iterable $participants): void
    {
        $this->participants = $participants;
    }

    public function getActionProtections(): iterable
    {
        return $this->actionProtections;
    }

    public function addActionProtection(Mesurement $mesurement): void
    {
        $this->actionProtections[] = $mesurement;
    }

    public function removeActionProtection(Mesurement $mesurement): void
    {
        $key = \array_search($mesurement, $this->actionProtections, true);

        if (false === $key) {
            return;
        }

        unset($this->actionProtections[$key]);
    }

    public function isComplete(): bool
    {
        return $this->complete;
    }

    public function setComplete(bool $complete): void
    {
        $this->complete = $complete;
    }
}
