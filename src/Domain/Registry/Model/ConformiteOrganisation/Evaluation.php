<?php

namespace App\Domain\Registry\Model\ConformiteOrganisation;

use App\Domain\User\Model\Collectivity;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Evaluation
{
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
     * @var Collectivity
     */
    private $organisation;

    /**
     * @var iterable
     */
    private $conformites;

    /**
     * @var string
     */
    private $pilote;

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

    public function setParticipants(iterable $participants): void
    {
        $this->participants = $participants;
    }

    public function isComplete(): bool
    {
        return $this->complete;
    }

    public function setComplete(bool $complete): void
    {
        $this->complete = $complete;
    }

    public function getOrganisation(): Collectivity
    {
        return $this->organisation;
    }

    public function setOrganisation(Collectivity $organisation): void
    {
        $this->organisation = $organisation;
    }

    public function __toString()
    {
        // TODO TMP toString
        return $this->organisation->getName();
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

    public function getPilote(): string
    {
        return $this->pilote;
    }

    public function setPilote(string $pilote): void
    {
        $this->pilote = $pilote;
    }
}
