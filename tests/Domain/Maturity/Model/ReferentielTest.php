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

namespace App\Tests\Domain\Maturity\Model;

use App\Application\Traits\Model\HistoryTrait;
use App\Domain\Maturity\Model\Referentiel;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

class ReferentielTest extends TestCase
{
    public function testConstruct()
    {
        $model = new Referentiel();

        $this->assertInstanceOf(UuidInterface::class, $model->getId());
        $this->assertEquals(new ArrayCollection(), $model->getDomains());
        $this->assertEquals(new ArrayCollection(), $model->getAuthorizedCollectivityTypes());
        $this->assertEquals(new ArrayCollection(), $model->getAuthorizedCollectivities());
    }

    public function testTraits()
    {
        $model = new Referentiel();

        $uses = \class_uses($model);
        $this->assertTrue(\in_array(HistoryTrait::class, $uses));
    }

    public function testToString()
    {
        $model = new Referentiel();
        $model->setName('test');

        $this->assertEquals('test', $model->__toString());
    }

    public function testToStringLongString()
    {
        $model = new Referentiel();
        $model->setName('loremipsumdolorsitametloremipsumdolorsitametloremipsumdolorsitametloremipsumdolorsitamet');

        $this->assertEquals('loremipsumdolorsitametloremipsumdolorsitametloremipsumdolorsitametloremipsumdolorsita...', $model->__toString());
    }
}
