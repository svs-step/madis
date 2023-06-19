<?php

namespace App\Domain\Reporting\Model;

use App\Domain\Reporting\Dictionary\LogJournalActionDictionary;
use App\Domain\User\Model\Collectivity;
use Ramsey\Uuid\Uuid;

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
     * @var string
     */
    private $userFullName;

    /**
     * @var string
     */
    private $userEmail;

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
     * @see LogJournalSubjectDictionary
     *
     * @var string
     */
    private $subjectType;

    /**
     * Can be null on delete action.
     *
     * @var string
     */
    private $subjectId;

    /**
     * @var string
     */
    private $subjectName;

    /**
     * @var bool
     */
    private $isDeleted;

    public function __construct(
        Collectivity $collectivity,
        string $userFullName,
        string $userEmail,
        string $action,
        string $subjectType,
        string $subjectId,
        string $subjectName
    ) {
        $this->id           = Uuid::uuid4();
        $this->collectivity = $collectivity;
        $this->userFullName = $userFullName;
        $this->userEmail    = $userEmail;
        $this->date         = new \DateTimeImmutable();
        $this->action       = $action;
        $this->subjectType  = $subjectType;
        $this->subjectId    = $subjectId;
        $this->subjectName  = $subjectName;
        $this->isDeleted    = false;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCollectivity(): Collectivity
    {
        return $this->collectivity;
    }

    public function getUserFullName(): string
    {
        return $this->userFullName;
    }

    public function getUserEmail(): string
    {
        return $this->userEmail;
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

    public function getSubjectId(): string
    {
        return $this->subjectId;
    }

    public function getSubjectName(): string
    {
        return $this->subjectName;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }
}
