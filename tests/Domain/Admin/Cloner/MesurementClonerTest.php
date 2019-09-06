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

use App\Domain\Admin\Cloner\ContractorCloner;
use App\Domain\Admin\Cloner\MesurementCloner;
use App\Domain\Registry\Model;
use App\Domain\User\Model as UserModel;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class MesurementClonerTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var EntityManagerInterface
     */
    private $entityManagerProphecy;

    /**
     * @var ContractorCloner
     */
    private $sut;

    protected function setUp(): void
    {
        $this->entityManagerProphecy = $this->prophesize(EntityManagerInterface::class);

        $this->sut = new MesurementCloner(
            $this->entityManagerProphecy->reveal()
        );
    }

    /**
     * Test cloneReferentForCollectivity
     * The referent is empty.
     */
    public function testCloneReferentForCollectivityEmpty(): void
    {
        $collectivity = new UserModel\Collectivity();
        $referent     = new Model\Mesurement();
        $referent->setName('name');
        $referent->setType('type');

        /** @var Model\Mesurement $cloned */
        $cloned = $this->invokeMethod(
            $this->sut,
            'cloneReferentForCollectivity',
            [$referent, $collectivity]
        );

        $this->assertEquals($referent->getName(), $cloned->getName());
        $this->assertEquals($referent->getType(), $cloned->getType());
        $this->assertNull($cloned->getDescription());
        $this->assertNull($cloned->getCost());
        $this->assertNull($cloned->getCharge());
        $this->assertNull($cloned->getStatus());
        $this->assertNull($cloned->getPlanificationDate());
        $this->assertNull($cloned->getComment());
        $this->assertEquals($collectivity, $cloned->getCollectivity());
        $this->assertEquals($referent, $cloned->getClonedFrom());
    }

    /**
     * Test cloneReferentForCollectivity
     * The referent is full filled.
     *
     * @throws \ReflectionException
     */
    public function testCloneReferentForCollectivityFullFilled(): void
    {
        $collectivity = new UserModel\Collectivity();
        $referent     = new Model\Mesurement();
        $referent->setName('name');
        $referent->setType('type');
        $referent->setDescription('description');
        $referent->setCost('cost');
        $referent->setCharge('charge');
        $referent->setStatus('status');
        $referent->setPlanificationDate(new \DateTime());
        $referent->setComment('comment');

        /** @var Model\Mesurement $cloned */
        $cloned = $this->invokeMethod(
            $this->sut,
            'cloneReferentForCollectivity',
            [$referent, $collectivity]
        );

        $this->assertEquals($referent->getName(), $cloned->getName());
        $this->assertEquals($referent->getType(), $cloned->getType());
        $this->assertEquals($referent->getDescription(), $cloned->getDescription());
        $this->assertEquals($referent->getCost(), $cloned->getCost());
        $this->assertEquals($referent->getCharge(), $cloned->getCharge());
        $this->assertEquals($referent->getStatus(), $cloned->getStatus());
        $this->assertEquals($referent->getPlanificationDate(), $cloned->getPlanificationDate());
        $this->assertEquals($referent->getComment(), $cloned->getComment());
        $this->assertEquals($collectivity, $cloned->getCollectivity());
        $this->assertEquals($referent, $cloned->getClonedFrom());
    }
}
