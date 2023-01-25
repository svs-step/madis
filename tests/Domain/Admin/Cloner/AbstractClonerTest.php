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

use App\Domain\Admin\Cloner\AbstractCloner;
use App\Domain\Admin\Cloner\ClonerInterface;
use App\Domain\Admin\Dictionary\DuplicationTypeDictionary;
use App\Domain\Admin\Model\DuplicatedObject;
use App\Domain\Admin\Model\Duplication;
use App\Domain\Registry\Model;
use App\Domain\User\Model as UserModel;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Class AbstractClonerTest.
 */
class AbstractClonerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var EntityManagerInterface
     */
    private $entityManagerProphecy;

    /**
     * @var AbstractCloner
     */
    private $sut;

    protected function setUp(): void
    {
        $this->entityManagerProphecy = $this->prophesize(EntityManagerInterface::class);

        $this->sut = new DummyAbstractClonerTest(
            $this->entityManagerProphecy->reveal()
        );

        parent::setUp();
    }

    /**
     * Test that class extends right classes/interfaces.
     */
    public function testInstanceOf(): void
    {
        $this->assertInstanceOf(ClonerInterface::class, $this->sut);
    }

    /**
     * Data provider
     * Construct Duplication Model objects.
     *
     * @throws \Exception
     */
    public function dataProviderDuplicationModel(): array
    {
        $treatment          = new Model\Treatment();
        $type               = DuplicationTypeDictionary::KEY_TREATMENT;
        $sourceCollectivity = new UserModel\Collectivity();
        $duplicatedObjects  = [
            new DuplicatedObject(
                new Duplication(
                    DuplicationTypeDictionary::KEY_TREATMENT,
                    $sourceCollectivity
                ),
                $sourceCollectivity,
                $treatment->getId()->toString()
            ),
        ];
        $targetCollectivities = [
            new UserModel\Collectivity(),
            new UserModel\Collectivity(),
        ];

        $data = [
            $treatment,
        ];
        $duplication = new Duplication($type, $sourceCollectivity, $targetCollectivities, $duplicatedObjects);
        foreach ($data as $dataItem) {
            $duplication->addData($dataItem);
        }

        $duplicatedObjects = [
            new DuplicatedObject($duplication, $targetCollectivities[0], $treatment->getId()->toString()),
            new DuplicatedObject($duplication, $targetCollectivities[1], $treatment->getId()->toString()),
        ];
        foreach ($duplicatedObjects as $duplicatedObject) {
            $duplication->addDuplicatedObjet($duplicatedObject);
        }

        return [
            [
                $duplication,
            ],
        ];
    }

    /**
     * Test clone
     * Clone with target option PER_TYPE.
     *
     * @dataProvider dataProviderDuplicationModel
     *
     * @throws \Exception
     */
    public function testClone(Duplication $duplication): void
    {
        // Every data must be cloned for every target collectivities
        // Then count how much new objects must be persist
        $nbExpectedNewObjects = \count($duplication->getTargetCollectivities()) * \count($duplication->getData());
        $this->entityManagerProphecy->persist(Argument::type(Model\Treatment::class))->shouldBeCalledTimes($nbExpectedNewObjects);
        $this->entityManagerProphecy->flush()->shouldBeCalledTimes(1);

        $this->sut->clone($duplication);
    }

    /**
     * Test cloneToSpecifiedTarget
     * The target collectivity exists in Duplication model.
     *
     * @dataProvider dataProviderDuplicationModel
     */
    public function testCloneToSpecifiedTarget(Duplication $duplication): void
    {
        $targetCollectivities = $duplication->getTargetCollectivities();
        $collectivity         = \end($targetCollectivities);

        // Every data must be cloned for specified target collectivities
        // Then count how much new objects must be persist
        $nbExpectedNewObjects = \count($duplication->getData());
        $this->entityManagerProphecy->persist(Argument::type(Model\Treatment::class))->shouldBeCalledTimes($nbExpectedNewObjects);
        $this->entityManagerProphecy->flush()->shouldBeCalledTimes(1);

        $this->sut->cloneToSpecifiedTarget($duplication, $collectivity);
    }
}

class DummyAbstractClonerTest extends AbstractCloner
{
    /**
     * @param object $referent
     *
     * @throws \Exception
     *
     * @return Model\Treatment
     */
    protected function cloneReferentForCollectivity($referent, UserModel\Collectivity $collectivity)
    {
        // Mock cloning, this will be test in child classes
        return new Model\Treatment();
    }
}
