<?php

namespace App\Domain\Registry\Model\ConformiteOrganisation;

use App\Application\Traits\Model\CollectivityTrait;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Evaluation
{
    use CollectivityTrait;

    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var \DateTime|null
     */
    private $date;

    /**
     * @var iterable
     */
    private $participants;

    /**
     * Determine if the evaluation is complete, or if it's only a draft.
     *
     * @var bool
     */
    private $complete;

    /**
     * @var iterable
     */
    private $conformites;

    public function __construct()
    {
        $this->id           = Uuid::uuid4();
        $this->participants = [];
        $this->complete     = false;
        $this->conformites  = [];
    }

    public function getId()
    {
        return $this->id;
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

    public function addParticipant(Participant $participant): void
    {
        $this->participants[] = $participant;
        $participant->setEvaluation($this);
    }

    public function removeParticipant(Participant $participant)
    {
        $key = \array_search($participant, iterable_to_array($this->participants), true);

        if (false === $key) {
            return;
        }

        unset($this->participants[$key]);
    }

    public function isComplete(): bool
    {
        return $this->complete;
    }

    public function setComplete(bool $complete): void
    {
        $this->complete = $complete;
    }

    public function __toString()
    {
        // TODO TMP toString
        return $this->collectivity->getName();
    }

    public function addConformite(Conformite $conformite): void
    {
        $this->conformites[] = $conformite;
        $conformite->setEvaluation($this);
    }

    public function removeConformite(Conformite $conformite): void
    {
        $key = \array_search($conformite, $this->conformites, true);

        if (false === $key) {
            return;
        }

        unset($this->$conformite[$key]);
    }

    public function getConformites(): iterable
    {
        return $this->conformites;
    }
}
