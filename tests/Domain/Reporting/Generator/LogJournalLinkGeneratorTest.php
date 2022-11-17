<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace App\Tests\Domain\Reporting\Generator;

use App\Domain\Reporting\Dictionary\LogJournalSubjectDictionary;
use App\Domain\Reporting\Generator\LogJournalLinkGenerator;
use App\Domain\Reporting\Model\LogJournal;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Routing\RouterInterface;

class LogJournalLinkGeneratorTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var LogJournalLinkGenerator
     */
    private $generator;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    protected function setUp(): void
    {
        $this->router           = $this->prophesize(RouterInterface::class);
        $this->entityManager    = $this->prophesize(EntityManagerInterface::class);

        $this->generator = new LogJournalLinkGenerator($this->router->reveal(), $this->entityManager->reveal());
    }

    public function testItReturnDeleteLabelOnNullSubject()
    {
        $logJournal = $this->prophesize(LogJournal::class);
        $logJournal->isDeleted()->shouldBeCalled()->willReturn(true);

        $this->assertSame(LogJournalLinkGenerator::DELETE_LABEL, $this->generator->getLink($logJournal->reveal()));
    }

    /**
     * @dataProvider userSubjectTypes
     */
    public function testItGenerateUrlForUserLogJournal(string $subjectType)
    {
        $logJournal = $this->prophesize(LogJournal::class);
        $logJournal->getSubjectId()->shouldBeCalled()->willReturn(Uuid::uuid4());
        $logJournal->isDeleted()->shouldBeCalled()->willReturn(false);
        $logJournal->getSubjectType()->shouldBeCalled()->willReturn($subjectType);

        $this->router->generate('user_user_edit', Argument::cetera())->shouldBeCalled();

        $this->generator->getLink($logJournal->reveal());
    }

    /**
     * @dataProvider editViewOnly
     */
    public function testItGenerateUrlForEditViewOnlySubject(string $subjectType)
    {
        $logJournal = $this->prophesize(LogJournal::class);
        $logJournal->getSubjectId()->shouldBeCalled()->willReturn(Uuid::uuid4());
        $logJournal->isDeleted()->shouldBeCalled()->willReturn(false);
        $logJournal->getSubjectType()->shouldBeCalled()->willReturn($subjectType);

        $this->router->generate($subjectType . '_edit', Argument::cetera())->shouldBeCalled();

        $this->generator->getLink($logJournal->reveal());
    }

    public function editViewOnly()
    {
        return [
            [LogJournalSubjectDictionary::REGISTRY_CONFORMITE_TRAITEMENT],
            [LogJournalSubjectDictionary::REGISTRY_PROOF],
            [LogJournalSubjectDictionary::MATURITY_SURVEY],
        ];
    }

    public function testItGenerateUrlForEvaluationSubject()
    {
        $logJournal = $this->prophesize(LogJournal::class);
        $logJournal->getSubjectId()->shouldBeCalled()->willReturn(Uuid::uuid4());
        $logJournal->isDeleted()->shouldBeCalled()->willReturn(false);
        $logJournal->getSubjectType()->shouldBeCalled()->willReturn(LogJournalSubjectDictionary::REGISTRY_CONFORMITE_ORGANISATION_EVALUATION);

        $this->router->generate('registry_conformite_organisation_edit', Argument::cetera())->shouldBeCalled();

        $this->generator->getLink($logJournal->reveal());
    }

    /**
     * @dataProvider showViewOnly
     */
    public function testItGenerateUrlForShowViewOnlySubject(string $subjectType)
    {
        $logJournal = $this->prophesize(LogJournal::class);
        $logJournal->getSubjectId()->shouldBeCalled()->willReturn(Uuid::uuid4());
        $logJournal->isDeleted()->shouldBeCalled()->willReturn(false);
        $logJournal->getSubjectType()->shouldBeCalled()->willReturn($subjectType);

        $this->router->generate($subjectType . '_show', Argument::cetera())->shouldBeCalled();

        $this->generator->getLink($logJournal->reveal());
    }

    public function showViewOnly()
    {
        return [
            [LogJournalSubjectDictionary::REGISTRY_TREATMENT],
            [LogJournalSubjectDictionary::REGISTRY_CONTRACTOR],
            [LogJournalSubjectDictionary::REGISTRY_REQUEST],
            [LogJournalSubjectDictionary::REGISTRY_REQUEST],
            [LogJournalSubjectDictionary::REGISTRY_MESUREMENT],
        ];
    }

    public function userSubjectTypes()
    {
        return [
            [LogJournalSubjectDictionary::USER_USER],
            [LogJournalSubjectDictionary::USER_EMAIL],
            [LogJournalSubjectDictionary::USER_PASSWORD],
            [LogJournalSubjectDictionary::USER_FIRSTNAME],
            [LogJournalSubjectDictionary::USER_LASTNAME],
        ];
    }
}
