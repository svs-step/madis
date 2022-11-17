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

namespace App\Tests\Domain\Admin\Cloner;

use App\Domain\Admin\Cloner;
use App\Domain\Admin\Dictionary\DuplicationTypeDictionary;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ClonerProviderTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var Cloner\ClonerInterface
     */
    private $treatmentClonerProphecy;

    /**
     * @var Cloner\ClonerInterface
     */
    private $contractorClonerProphecy;

    /**
     * @var Cloner\ClonerInterface
     */
    private $mesurementClonerProphecy;

    /**
     * @var Cloner\ClonerProvider
     */
    private $sut;

    protected function setUp(): void
    {
        $this->treatmentClonerProphecy  = $this->prophesize(Cloner\TreatmentCloner::class);
        $this->contractorClonerProphecy = $this->prophesize(Cloner\ContractorCloner::class);
        $this->mesurementClonerProphecy = $this->prophesize(Cloner\MesurementCloner::class);

        $this->sut = new Cloner\ClonerProvider(
            $this->treatmentClonerProphecy->reveal(),
            $this->contractorClonerProphecy->reveal(),
            $this->mesurementClonerProphecy->reveal()
        );
    }

    /**
     * Test getCloner
     * Check every case.
     */
    public function testGetCloner(): void
    {
        $this->assertInstanceOf(
            Cloner\TreatmentCloner::class,
            $this->sut->getCloner(DuplicationTypeDictionary::KEY_TREATMENT)
        );

        $this->assertInstanceOf(
            Cloner\ContractorCloner::class,
            $this->sut->getCloner(DuplicationTypeDictionary::KEY_CONTRACTOR)
        );

        $this->assertInstanceOf(
            Cloner\MesurementCloner::class,
            $this->sut->getCloner(DuplicationTypeDictionary::KEY_MESUREMENT)
        );
    }

    /**
     * Test getCloner
     * Check default case.
     */
    public function testGetClonerDefaultCase(): void
    {
        $this->expectException(\RuntimeException::class);
        $type = 'ThisIsNotAnExpectedTypeThenItReachDefaultSwitchOption';

        $this->sut->getCloner($type);
    }
}
