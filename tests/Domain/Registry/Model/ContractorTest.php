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

namespace App\Tests\Domain\Registry\Model;

use App\Application\Traits\Model\CollectivityTrait;
use App\Application\Traits\Model\CreatorTrait;
use App\Application\Traits\Model\HistoryTrait;
use App\Domain\Registry\Model\Contractor;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

class ContractorTest extends TestCase
{
    public function testConstruct()
    {
        $model = new Contractor();

        $this->assertInstanceOf(UuidInterface::class, $model->getId());
        $this->assertFalse($model->isContractualClausesVerified());
        $this->assertFalse($model->isAdoptedSecurityFeatures());
        $this->assertFalse($model->isMaintainsTreatmentRegister());
        $this->assertFalse($model->isSendingDataOutsideEu());
        $this->assertEquals([], $model->getTreatments());
    }

    public function testTraits()
    {
        $model = new Contractor();

        $uses = \class_uses($model);
        $this->assertTrue(\in_array(CollectivityTrait::class, $uses));
        $this->assertTrue(\in_array(CreatorTrait::class, $uses));
        $this->assertTrue(\in_array(HistoryTrait::class, $uses));
    }
}
