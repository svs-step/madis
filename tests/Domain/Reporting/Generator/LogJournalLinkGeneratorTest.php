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

use App\Domain\Maturity\Model\Survey;
use App\Domain\Registry\Model\ConformiteOrganisation\Evaluation;
use App\Domain\Registry\Model\ConformiteTraitement\ConformiteTraitement;
use App\Domain\Registry\Model\Contractor;
use App\Domain\Registry\Model\Mesurement;
use App\Domain\Registry\Model\Proof;
use App\Domain\Registry\Model\Request;
use App\Domain\Registry\Model\Treatment;
use App\Domain\Reporting\Dictionary\LogJournalSubjectDictionary;
use App\Domain\Reporting\Generator\LogJournalLinkGenerator;
use App\Domain\Reporting\Model\LogJournal;
use App\Domain\User\Model\User;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Routing\RouterInterface;

class LogJournalLinkGeneratorTest extends TestCase
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var LogJournalLinkGenerator
     */
    private $generator;

    protected function setUp()
    {
        $this->router = $this->prophesize(RouterInterface::class);

        $this->generator = new LogJournalLinkGenerator($this->router->reveal());
    }

    public function testItReturnDeleteLabelOnNullSubject()
    {
        $logJournal = $this->prophesize(LogJournal::class);
        $logJournal->getSubject()->shouldBeCalled()->willReturn(null);

        $this->assertSame(LogJournalLinkGenerator::DELETE_LABEL, $this->generator->getLink($logJournal->reveal()));
    }

    public function testItGenerateUrlForUserLogJournal()
    {
        $logJournal = $this->prophesize(LogJournal::class);
        $logJournal->getSubject()->shouldBeCalled()->willReturn(new User());

        $this->router->generate('user_user_edit', Argument::cetera())->shouldBeCalled();

        $this->generator->getLink($logJournal->reveal());
    }

    /**
     * @dataProvider editViewOnly
     */
    public function testItGenerateUrlForEditViewOnlySubject(string $className, string $subjectType)
    {
        $logJournal = $this->prophesize(LogJournal::class);
        $logJournal->getSubject()->shouldBeCalled()->willReturn(new $className());
        $logJournal->getSubjectType()->shouldBeCalled()->willReturn($subjectType);

        $this->router->generate($subjectType . '_edit', Argument::cetera())->shouldBeCalled();

        $this->generator->getLink($logJournal->reveal());
    }

    public function editViewOnly()
    {
        return [
            [ConformiteTraitement::class, LogJournalSubjectDictionary::REGISTRY_CONFORMITE_TRAITEMENT],
            [Proof::class, LogJournalSubjectDictionary::REGISTRY_PROOF],
            [Survey::class, LogJournalSubjectDictionary::MATURITY_SURVEY],
        ];
    }

    public function testItGenerateUrlForEvaluationSubject()
    {
        $logJournal = $this->prophesize(LogJournal::class);
        $logJournal->getSubject()->shouldBeCalled()->willReturn(new Evaluation());

        $this->router->generate('registry_conformite_organisation_edit', Argument::cetera())->shouldBeCalled();

        $this->generator->getLink($logJournal->reveal());
    }

    /**
     * @dataProvider showViewOnly
     */
    public function testItGenerateUrlForShowViewOnlySubject(string $className, string $subjectType)
    {
        $logJournal = $this->prophesize(LogJournal::class);
        $logJournal->getSubject()->shouldBeCalled()->willReturn(new $className());
        $logJournal->getSubjectType()->shouldBeCalled()->willReturn($subjectType);

        $this->router->generate($subjectType . '_show', Argument::cetera())->shouldBeCalled();

        $this->generator->getLink($logJournal->reveal());
    }

    public function showViewOnly()
    {
        return [
            [Treatment::class, LogJournalSubjectDictionary::REGISTRY_TREATMENT],
            [Contractor::class, LogJournalSubjectDictionary::REGISTRY_CONTRACTOR],
            [Request::class, LogJournalSubjectDictionary::REGISTRY_REQUEST],
            [Request::class, LogJournalSubjectDictionary::REGISTRY_REQUEST],
            [Mesurement::class, LogJournalSubjectDictionary::REGISTRY_MESUREMENT],
        ];
    }
}
