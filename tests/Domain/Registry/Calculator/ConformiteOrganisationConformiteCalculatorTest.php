<?php

namespace App\Tests\Domain\Registry\Calculator;

use App\Domain\Registry\Calculator\ConformiteOrganisationConformiteCalculator;
use App\Domain\Registry\Dictionary\ConformiteOrganisation\ReponseDictionary;
use App\Domain\Registry\Model\ConformiteOrganisation\Conformite;
use App\Domain\Registry\Model\ConformiteOrganisation\Evaluation;
use App\Domain\Registry\Model\ConformiteOrganisation\Reponse;
use PHPUnit\Framework\TestCase;

class ConformiteOrganisationConformiteCalculatorTest extends TestCase
{
    private $calculator;

    public function setUp()
    {
        $this->calculator = new ConformiteOrganisationConformiteCalculator();
    }

    public function testItCalculConformite()
    {
        $evaluation = $this->prophesize(Evaluation::class);

        $conformite1 = $this->prophesize(Conformite::class);
        $reponse1    = $this->prophesize(Reponse::class);
        $reponse1->getReponse()->willReturn(ReponseDictionary::MESURABLE);
        $reponse2 = $this->prophesize(Reponse::class);
        $conformite1->getReponses()->shouldBeCalled()->willReturn([$reponse1->reveal(), $reponse2->reveal()]);
        $reponse2->getReponse()->willReturn(ReponseDictionary::QUASI_CONFORME);

        $conformite2 = $this->prophesize(Conformite::class);
        $reponse3    = $this->prophesize(Reponse::class);
        $reponse3->getReponse()->willReturn(1);
        $conformite2->getReponses()->willReturn([$reponse3]);

        $conformite1->setConformite(3.5)->shouldBeCalled();
        $conformite2->setConformite(1)->shouldBeCalled();

        $evaluation->getConformites()->willReturn([$conformite1, $conformite2]);

        $this->calculator->calculEvaluationConformites($evaluation->reveal());
    }
}
