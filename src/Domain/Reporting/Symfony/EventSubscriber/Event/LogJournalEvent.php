<?php

namespace App\Domain\Reporting\Symfony\EventSubscriber\Event;

use App\Domain\Reporting\Model\LoggableSubject;
use App\Domain\Reporting\Model\LogJournal;
use Symfony\Contracts\EventDispatcher\Event;

class LogJournalEvent extends Event
{
    /**
     * @var LogJournal
     */
    protected $logJournal;

    /**
     * @var LoggableSubject|null
     */
    protected $subject;

    public function __construct(LogJournal $logJournal, LoggableSubject $subject = null)
    {
        $this->logJournal = $logJournal;
        $this->subject    = $subject;
    }

    public function getLogJournal(): LogJournal
    {
        return $this->logJournal;
    }

    public function getSubject(): ?LoggableSubject
    {
        return $this->subject;
    }
}
