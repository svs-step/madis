<?php

declare(strict_types=1);

namespace App\Tests\Domain\Admin\Hydrator;

use App\Domain\Admin\Dictionary\DuplicationTypeDictionary;
use App\Domain\Admin\Hydrator\DuplicationHydrator;
use App\Domain\Admin\Model\Duplication;
use App\Domain\Registry\Model as RegistryModel;
use App\Domain\Registry\Repository as RegistryRepository;
use App\Domain\User\Model as UserModel;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class DuplicationHydratorTest extends TestCase
{
    /**
     * @var RegistryRepository\Treatment
     */
    private $treatmentRepositoryProphecy;

    /**
     * @var RegistryRepository\Contractor
     */
    private $contractorRepositoryProphecy;

    /**
     * @var RegistryRepository\Mesurement
     */
    private $mesurementRepositoryProphecy;

    /**
     * @var DuplicationHydrator
     */
    private $sut;

    protected function setUp(): void
    {
        $this->treatmentRepositoryProphecy  = $this->prophesize(RegistryRepository\Treatment::class);
        $this->contractorRepositoryProphecy = $this->prophesize(RegistryRepository\Contractor::class);
        $this->mesurementRepositoryProphecy = $this->prophesize(RegistryRepository\Mesurement::class);

        $this->sut = new DuplicationHydrator(
            $this->treatmentRepositoryProphecy->reveal(),
            $this->contractorRepositoryProphecy->reveal(),
            $this->mesurementRepositoryProphecy->reveal()
        );

        parent::setUp();
    }

    /**
     * Data Provider
     * Create duplication objects.
     *
     * @throws \Exception
     */
    public function dataProviderDuplication(): array
    {
        $sourceCollectivity   = new UserModel\Collectivity();
        $treatmentDuplication = new Duplication(
            DuplicationTypeDictionary::KEY_TREATMENT,
            $sourceCollectivity
        );
        $contractorDuplication = new Duplication(
            DuplicationTypeDictionary::KEY_TREATMENT,
            $sourceCollectivity
        );
        $mesurementDuplication = new Duplication(
            DuplicationTypeDictionary::KEY_TREATMENT,
            $sourceCollectivity
        );

        return [
            [
                $treatmentDuplication,
            ],
            [
                $contractorDuplication,
            ],
            [
                $mesurementDuplication,
            ],
        ];
    }

    /**
     * Test hydrate
     * Hydrate duplication data from dataIds
     * Test every available duplication Type.
     *
     * @dataProvider dataProviderDuplication
     *
     * @throws \Exception
     */
    public function testHydrateAvailableTypes(Duplication $duplication): void
    {
        $dataIds = [
            'uuid1',
            'uuid2',
            'uuid3',
        ];

        foreach ($dataIds as $dataId) {
            $duplication->addDataId($dataId);
        }

        switch ($duplication->getType()) {
            case DuplicationTypeDictionary::KEY_TREATMENT:
                foreach ($dataIds as $dataId) {
                    $this->treatmentRepositoryProphecy->findOneById($dataId)->shouldBeCalled()->willReturn(new RegistryModel\Treatment());
                }
                $this->contractorRepositoryProphecy->findOneById(Argument::cetera())->shouldNotBeCalled();
                $this->mesurementRepositoryProphecy->findOneById(Argument::cetera())->shouldNotBeCalled();
                break;
            case DuplicationTypeDictionary::KEY_CONTRACTOR:
                $this->treatmentRepositoryProphecy->findOneById(Argument::cetera())->shouldNotBeCalled();
                foreach ($dataIds as $dataId) {
                    $this->contractorRepositoryProphecy->findOneById($dataId)->shouldBeCalled()->willReturn(new RegistryModel\Treatment());
                }
                $this->mesurementRepositoryProphecy->findOneById(Argument::cetera())->shouldNotBeCalled();
                break;
            case DuplicationTypeDictionary::KEY_MESUREMENT:
                $this->treatmentRepositoryProphecy->findOneById(Argument::cetera())->shouldNotBeCalled();
                $this->contractorRepositoryProphecy->findOneById(Argument::cetera())->shouldNotBeCalled();
                foreach ($dataIds as $dataId) {
                    $this->mesurementRepositoryProphecy->findOneById($dataId)->shouldBeCalled()->willReturn(new RegistryModel\Treatment());
                }
                break;
            default:
                throw new \RuntimeException('You got an error in your test. Type ' . $duplication->getTargetCollectivities() . ' is not allowed.');
        }

        $this->assertCount(0, $duplication->getData());
        $this->sut->hydrate($duplication);
        $this->assertCount(\count($dataIds), $duplication->getData());
    }

    /**
     * Test hydrate
     * We try to hydrate a bad duplication type.
     *
     * @expectedException \RuntimeException
     *
     * @throws \Exception
     */
    public function testHydrateUnavailableType(): void
    {
        $duplication = new Duplication(
            'ThisIsNotAnExpectedAndAvailableType',
            new UserModel\Collectivity()
        );

        $this->treatmentRepositoryProphecy->findOneById(Argument::cetera())->shouldNotBeCalled();
        $this->contractorRepositoryProphecy->findOneById(Argument::cetera())->shouldNotBeCalled();
        $this->mesurementRepositoryProphecy->findOneById(Argument::cetera())->shouldNotBeCalled();

        $this->sut->hydrate($duplication);
    }
}
