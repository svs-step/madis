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
use App\Domain\Registry\Model\Embeddable\ComplexChoice;
use App\Domain\Registry\Model\Embeddable\Delay;
use App\Domain\Registry\Model\Treatment;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

class TreatmentTest extends TestCase
{
    public function testConstruct()
    {
        $model = new Treatment();

        $this->assertInstanceOf(UuidInterface::class, $model->getId());
        $this->assertEquals([], $model->getConcernedPeople());
        $this->assertEquals([], $model->getDataCategories());
        $this->assertInstanceOf(ArrayCollection::class, $model->getContractors());
        $this->assertInstanceOf(Delay::class, $model->getDelay());
        $this->assertInstanceOf(ComplexChoice::class, $model->getSecurityAccessControl());
        $this->assertInstanceOf(ComplexChoice::class, $model->getSecurityTracability());
        $this->assertInstanceOf(ComplexChoice::class, $model->getSecuritySaving());
        $this->assertInstanceOf(ComplexChoice::class, $model->getSecurityUpdate());
        $this->assertInstanceOf(ComplexChoice::class, $model->getSecurityOther());
        $this->assertFalse($model->isSystematicMonitoring());
        $this->assertFalse($model->isLargeScaleCollection());
        $this->assertFalse($model->isVulnerablePeople());
        $this->assertFalse($model->isDataCrossing());
        $this->assertTrue($model->isActive());
    }

    public function testTraits()
    {
        $model = new Treatment();

        $uses = \class_uses($model);
        $this->assertTrue(\in_array(CollectivityTrait::class, $uses));
        $this->assertTrue(\in_array(CreatorTrait::class, $uses));
        $this->assertTrue(\in_array(HistoryTrait::class, $uses));
    }
}
