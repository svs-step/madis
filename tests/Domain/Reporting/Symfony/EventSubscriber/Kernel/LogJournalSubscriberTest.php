<?php

declare(strict_types=1);

namespace App\Tests\Domain\Reporting\Symfony\EventSubscriber\Kernel;

use App\Domain\Reporting\Dictionary\LogJournalActionDictionary;
use App\Domain\Reporting\Model\LoggableSubject;
use App\Domain\Reporting\Symfony\EventSubscriber\Event\LogJournalEvent;
use App\Domain\Reporting\Symfony\EventSubscriber\Kernel\LogJournalSubscriber;
use App\Infrastructure\ORM\Reporting\Repository\LogJournal;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LogJournalSubscriberTest extends TestCase
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \App\Domain\Reporting\Repository\LogJournal
     */
    private $logJournalRepository;

    /**
     * @var LogJournalSubscriber
     */
    private $sut;

    protected function setUp(): void
    {
        $this->entityManager        = $this->prophesize(EntityManagerInterface::class);
        $this->logJournalRepository = $this->prophesize(LogJournal::class);
        $this->sut                  = new LogJournalSubscriber($this->entityManager->reveal(), $this->logJournalRepository->reveal());
    }

    /**
     * Test instance of Subscriber.
     */
    public function testInstanceOf(): void
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->sut);
    }

    /**
     * Test getSubscribedEvents of current subscriber.
     */
    public function testGetSubscribedEvents(): void
    {
        $this->assertEquals(
            [
                LogJournalEvent::class => ['saveLogJournal'],
            ],
            $this->sut->getSubscribedEvents()
        );
    }

    public function testItSaveLogJournalOnNonDeleteAction(): void
    {
        $logJournal = $this->prophesize(\App\Domain\Reporting\Model\LogJournal::class);
        $logJournal->getAction()->shouldBeCalled()->willReturn('foo');
        $event = new LogJournalEvent($logJournal->reveal());

        $this->entityManager->persist($logJournal)->shouldBeCalled();
        $this->entityManager->flush()->shouldBeCalled();

        $this->sut->saveLogJournal($event);
    }

    public function testItSaveLogJournalWithDeleteAction(): void
    {
        $logJournal = $this->prophesize(\App\Domain\Reporting\Model\LogJournal::class);
        $logJournal->getAction()->shouldBeCalled()->willReturn(LogJournalActionDictionary::DELETE);
        $loggableSubject = new FooLoggableSubject();
        $event           = new LogJournalEvent($logJournal->reveal(), $loggableSubject);

        $this->entityManager->persist($logJournal)->shouldBeCalled();
        $this->entityManager->flush()->shouldBeCalled();

        $this->logJournalRepository->updateDeletedLog($event->getSubject())->shouldBeCalled();

        $this->sut->saveLogJournal($event);
    }
}

class FooLoggableSubject implements LoggableSubject
{
    public function __toString(): string
    {
        return 'foo';
    }

    public function getId()
    {
        return '1';
    }
}
