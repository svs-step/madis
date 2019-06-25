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
use App\Application\Traits\Model\SoftDeletableTrait;
use App\Domain\Registry\Model\Embeddable\RequestAnswer;
use App\Domain\Registry\Model\Embeddable\RequestApplicant;
use App\Domain\Registry\Model\Embeddable\RequestConcernedPeople;
use App\Domain\Registry\Model\Request;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

class RequestTest extends TestCase
{
    public function testConstruct()
    {
        $model = new Request();

        $this->assertInstanceOf(UuidInterface::class, $model->getId());
        $this->assertInstanceOf(\DateTime::class, $model->getDate());
        $this->assertInstanceOf(RequestApplicant::class, $model->getApplicant());
        $this->assertInstanceOf(RequestConcernedPeople::class, $model->getConcernedPeople());
        $this->assertInstanceOf(RequestAnswer::class, $model->getAnswer());
        $this->assertFalse($model->isComplete());
        $this->assertFalse($model->isLegitimateApplicant());
        $this->assertFalse($model->isLegitimateRequest());
    }

    public function testTraits()
    {
        $model = new Request();

        $uses = \class_uses($model);
        $this->assertTrue(\in_array(CollectivityTrait::class, $uses));
        $this->assertTrue(\in_array(CreatorTrait::class, $uses));
        $this->assertTrue(\in_array(HistoryTrait::class, $uses));
        $this->assertTrue(\in_array(SoftDeletableTrait::class, $uses));
    }
}
