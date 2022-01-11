<?php

declare(strict_types=1);

namespace App\Tests\Domain\Admin\Transformer;

use App\Domain\Admin\Dictionary\DuplicationTargetOptionDictionary;
use App\Domain\Admin\Dictionary\DuplicationTypeDictionary;
use App\Domain\Admin\DTO\DuplicationFormDTO;
use App\Domain\Admin\Hydrator\DuplicationHydrator;
use App\Domain\Admin\Model\Duplication;
use App\Domain\Admin\Transformer\DuplicationFormDTOTransformer;
use App\Domain\User\Dictionary\CollectivityTypeDictionary;
use App\Domain\User\Model as UserModel;
use App\Domain\User\Repository as UserRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class DuplicationFormDTOTransformerTest extends TestCase
{
    use ProphecyTrait;
    /**
     * @var UserRepository\Collectivity
     */
    private $collectivityRepositoryProphecy;

    /**
     * @var DuplicationHydrator
     */
    private $hydratorProphecy;

    /**
     * @var DuplicationFormDTOTransformer
     */
    private $sut;

    protected function setUp(): void
    {
        $this->collectivityRepositoryProphecy = $this->prophesize(UserRepository\Collectivity::class);
        $this->hydratorProphecy               = $this->prophesize(DuplicationHydrator::class);

        $this->sut = new DuplicationFormDTOTransformer(
            $this->collectivityRepositoryProphecy->reveal(),
            $this->hydratorProphecy->reveal()
        );

        parent::setUp();
    }

    /**
     * DataProvider
     * Create a fullfilled DTO object
     * Object is fullfilled to ensure every cases.
     *
     * @throws \Exception
     */
    public function dataProviderDTO(): array
    {
        $type                 = DuplicationTypeDictionary::KEY_TREATMENT;
        $sourceCollectivity   = new UserModel\Collectivity();
        $targetCollectivities = [
            new UserModel\Collectivity(),
            new UserModel\Collectivity(),
        ];
        $targetCollectivityTypes = [
            CollectivityTypeDictionary::getTypesKeys()[0],
            CollectivityTypeDictionary::getTypesKeys()[1],
        ];
        $data = [
            'uuid1',
            'uuid2',
            'uuid3',
        ];

        $dto = new DuplicationFormDTO();
        $dto->setType($type);
        $dto->setSourceCollectivity($sourceCollectivity);
        $dto->setTargetCollectivityTypes($targetCollectivityTypes);
        $dto->setTargetCollectivities($targetCollectivities);
        $dto->setData($data);

        return [
            [
                $dto,
            ],
        ];
    }

    /**
     * Test toModelObject
     * Target option is `KEY_PER_COLLECTIVITY`.
     *
     * @dataProvider dataProviderDTO
     *
     * @throws \Exception
     */
    public function testToModelObjectPerCollectivity(DuplicationFormDTO $dto): void
    {
        $targetOption = DuplicationTargetOptionDictionary::KEY_PER_COLLECTIVITY;
        $dto->setTargetOption($targetOption);

        $targetCollectivitesIds = [
            $dto->getTargetCollectivities()[0]->getId()->toString(),
            $dto->getTargetCollectivities()[1]->getId()->toString(),
        ];

        $this->hydratorProphecy->hydrate(Argument::type(Duplication::class))->shouldBeCalled();
        $this->collectivityRepositoryProphecy->findByTypes(Argument::cetera())->shouldNotBeCalled();

        $model = $this->sut->toModelObject($dto);

        $this->assertInstanceOf(Duplication::class, $model);
        $this->assertEquals($dto->getType(), $model->getType());
        $this->assertEquals($dto->getSourceCollectivity(), $model->getSourceCollectivity());
        $this->assertEquals($dto->getData(), $model->getDataIds());
        // hydrator would set data to $model::$data but we don't check it here

        $modelCollectivitiesIds = [];
        foreach ($model->getTargetCollectivities() as $collectivity) {
            $modelCollectivitiesIds[] = $collectivity->getId()->toString();
        }

        $this->assertEquals($targetCollectivitesIds, $modelCollectivitiesIds);
    }

    /**
     * Test toModelObject
     * Target option is `KEY_PER_TYPE`.
     *
     * @dataProvider dataProviderDTO
     *
     * @throws \Exception
     */
    public function testToModelObjectPerType(DuplicationFormDTO $dto): void
    {
        $targetOption = DuplicationTargetOptionDictionary::KEY_PER_TYPE;
        $dto->setTargetOption($targetOption);
        $targetCollectivities = [
            new UserModel\Collectivity(),
            new UserModel\Collectivity(),
        ];
        $targetCollectivitiesIds = [
            $targetCollectivities[0]->getId()->toString(),
            $targetCollectivities[1]->getId()->toString(),
        ];

        $this->hydratorProphecy->hydrate(Argument::type(Duplication::class))->shouldBeCalled();
        $this->collectivityRepositoryProphecy
            ->findByTypes(
                $dto->getTargetCollectivityTypes(),
                $dto->getSourceCollectivity()
            )
            ->shouldBeCalled()
            ->willReturn($targetCollectivities)
        ;

        $model = $this->sut->toModelObject($dto);

        $this->assertInstanceOf(Duplication::class, $model);
        $this->assertEquals($dto->getType(), $model->getType());
        $this->assertEquals($dto->getSourceCollectivity(), $model->getSourceCollectivity());
        $this->assertEquals($dto->getData(), $model->getDataIds());
        // hydrator would set data to $model::$data but we don't check it here

        $ids = [];
        foreach ($model->getTargetCollectivities() as $collectivity) {
            $ids[] = $collectivity->getId()->toString();
        }

        $this->assertEquals($targetCollectivitiesIds, $targetCollectivitiesIds);
    }
}
