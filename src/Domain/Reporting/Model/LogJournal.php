<?php

namespace App\Domain\Reporting\Model;

use App\Domain\Reporting\Dictionary\LogJournalActionDictionary;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\User;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Contain all user actions (Create, update, delete, login, and update user account).
 */
class LogJournal
{
    /**
     * @var Uuid
     */
    private $id;

    /**
     * @var Collectivity
     */
    private $collectivity;

    /**
     * @var User
     */
    private $user;

    /**
     * @var \DateTimeImmutable
     */
    private $date;

    /**
     * @see LogJournalActionDictionary
     *
     * @var string
     */
    private $action;

    /**
     * @see LogJournalSubjectDictionar
     *
     * @var string
     */
    private $subjectType;

    /**
     * Can be null on delete action.
     *
     * @var LoggableSubject|null
     */
    private $subject;

    /*
     * Need to know the last name subject on delete action
     *
     * @var string
     */
    private $lastKnownName;

    public function __construct(
        Collectivity $collectivity,
        UserInterface $user,
        string $action,
        string $subjectType,
        LoggableSubject $subject = null,
        string $lastKnownName = null
    ) {
        $this->id            = Uuid::uuid4();
        $this->collectivity  = $collectivity;
        $this->user          = $user;
        $this->date          = new \DateTimeImmutable();
        $this->action        = $action;
        $this->subjectType   = $subjectType;
        $this->subject       = $subject;
        $this->lastKnownName = $lastKnownName;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCollectivity(): Collectivity
    {
        return $this->collectivity;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getSubjectType(): string
    {
        return $this->subjectType;
    }

    public function getSubject(): ?LoggableSubject
    {
        return $this->subject;
    }

    public function getLastKnownName(): ?string
    {
        return $this->lastKnownName;
    }
}
