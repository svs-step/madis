<?php

namespace App\Domain\Reporting\Symfony\EventSubscriber\Kernel;

use App\Domain\Reporting\Dictionary\LogJournalActionDictionary;
use App\Domain\Reporting\Symfony\EventSubscriber\Event\LogJournalEvent;
use App\Infrastructure\ORM\Reporting\Repository\LogJournal;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LogJournalSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \App\Domain\Reporting\Repository\LogJournal
     */
    private $logJournalRepository;

    public function __construct(EntityManagerInterface $entityManager, LogJournal $logJournalRepository)
    {
        $this->entityManager        = $entityManager;
        $this->logJournalRepository = $logJournalRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            LogJournalEvent::class => ['saveLogJournal'],
        ];
    }

    public function saveLogJournal(LogJournalEvent $event)
    {
        $logJournal = $event->getLogJournal();
        $this->entityManager->persist($logJournal);
        $this->entityManager->flush();

        if (LogJournalActionDictionary::DELETE === $logJournal->getAction()) {
            $this->logJournalRepository->updateSubjectIdWithGivenUuid($logJournal, $event->getSubject());
            $this->logJournalRepository->updateLastKnownNameEntriesForGivenSubject($event->getSubject());
        }
    }
}
