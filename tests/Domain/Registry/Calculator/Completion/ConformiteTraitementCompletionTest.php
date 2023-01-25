<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author ANODE <contact@agence-anode.fr>
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

namespace App\Tests\Domain\Registry\Calculator\Completion;

use App\Domain\Registry\Calculator\Completion\ConformiteTraitementCompletion;
use App\Domain\Registry\Model;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ConformiteTraitementCompletionTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ConformiteTraitementCompletion
     */
    private $calculator;

    public function setUp(): void
    {
        $this->calculator = new ConformiteTraitementCompletion();
    }

    public function testItCalculConformite()
    {
        $conformite               = $this->prophesize(Model\ConformiteTraitement\ConformiteTraitement::class);
        $reponseConforme          = $this->prophesize(Model\ConformiteTraitement\Reponse::class);
        $reponseNonConformeMineur = $this->prophesize(Model\ConformiteTraitement\Reponse::class);
        $reponseNonConformeMajeur = $this->prophesize(Model\ConformiteTraitement\Reponse::class);
        $action1                  = $this->prophesize(Model\Mesurement::class);

        $action1->getPlanificationDate()->shouldBeCalled()->willReturn(new \DateTime());

        $reponseConforme->isConforme()->willReturn(true);
        $reponseNonConformeMineur->isConforme()->willReturn(false);
        $reponseNonConformeMajeur->isConforme()->willReturn(false);
        $reponseNonConformeMineur->getActionProtections()->shouldBeCalled()->willReturn([$action1->reveal()]);
        $reponseNonConformeMajeur->getActionProtections()->shouldBeCalled()->willReturn([]);
        $conformite->getReponses()->shouldBeCalled()->willReturn([$reponseConforme->reveal(), $reponseNonConformeMineur->reveal(), $reponseNonConformeMajeur->reveal()]);

        $conformite->setNbConformes(1)->shouldBeCalled();
        $conformite->setNbNonConformesMineures(1)->shouldBeCalled();
        $conformite->setNbNonConformesMajeures(1)->shouldBeCalled();

        $this->calculator->setCalculsConformite($conformite->reveal());
    }
}
