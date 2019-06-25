<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author Donovan Bourlard <donovan@awkan.fr>
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

namespace App\Tests\Domain\Maturity\Calculator;

use App\Domain\Maturity\Calculator;
use App\Domain\Maturity\Model;
use PHPUnit\Framework\TestCase;

class MaturityTest extends TestCase
{
    /**
     * @var Calculator\Maturity
     */
    private $calculator;

    protected function setUp(): void
    {
        $this->calculator = new Calculator\Maturity();
    }

    public function testGenerateMaturityByDomain(): void
    {
        $question1 = new Model\Question();
        $question2 = new Model\Question();
        $question3 = new Model\Question();
        $question4 = new Model\Question();

        $domain1 = new Model\Domain();
        $domain1->addQuestion($question1);
        $domain2 = new Model\Domain();
        $domain2->addQuestion($question2);
        $domain3 = new Model\Domain();
        $domain3->addQuestion($question3);
        $domain3->addQuestion($question4);

        $maturity1 = new Model\Maturity();
        $maturity1->setDomain($domain1);
        $maturity2 = new Model\Maturity();
        $maturity2->setDomain($domain2);
        $maturity3 = new Model\Maturity();
        $maturity3->setDomain($domain3);

        $answer1 = new Model\Answer();
        $answer1->setQuestion($question1);
        $answer1->setResponse(2);
        $answer2 = new Model\Answer();
        $answer2->setQuestion($question2);
        $answer2->setResponse(0);
        $answer3 = new Model\Answer();
        $answer3->setQuestion($question3);
        $answer3->setResponse(2);
        $answer4 = new Model\Answer();
        $answer4->setQuestion($question4);
        $answer4->setResponse(1);

        $survey = new Model\Survey();
        // Maturity already set
        $survey->addMaturity($maturity1);
        // Link answers
        $survey->addAnswer($answer1);
        $survey->addAnswer($answer2);
        $survey->addAnswer($answer3);
        $survey->addAnswer($answer4);

        $result = $this->calculator->generateMaturityByDomain($survey);

        $this->assertTrue(isset($result[$domain1->getId()->toString()]));
        $this->assertTrue(isset($result[$domain2->getId()->toString()]));
        $this->assertTrue(isset($result[$domain3->getId()->toString()]));

        $this->assertEquals(
            \intval(\ceil($answer1->getResponse() / 14 * 5 * 10)),
            $result[$domain1->getId()->toString()]->getScore()
        );
        $this->assertEquals(
            \intval(\ceil($answer2->getResponse() / 14 * 5 * 10)),
            $result[$domain2->getId()->toString()]->getScore()
        );
        $this->assertEquals(
            \intval(\ceil(($answer3->getResponse() + $answer4->getResponse()) / 14 * 5 * 10)),
            $result[$domain3->getId()->toString()]->getScore()
        );
    }

    public function testGlobalScore(): void
    {
        $maturity1score = 2;
        $maturity2score = 3;

        $maturity1Prophecy = $this->prophesize(Model\Maturity::class);
        $maturity2Prophecy = $this->prophesize(Model\Maturity::class);

        $maturity1Prophecy->getScore()->shouldBeCalled()->willReturn($maturity1score);
        $maturity2Prophecy->getScore()->shouldBeCalled()->willReturn($maturity2score);

        $maturityList = [
            $maturity1Prophecy->reveal(),
            $maturity2Prophecy->reveal(),
        ];

        $this->assertEquals(3, $this->calculator->getGlobalScore($maturityList));
    }
}
