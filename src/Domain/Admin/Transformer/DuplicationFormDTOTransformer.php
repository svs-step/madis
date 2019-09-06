<?php

declare(strict_types=1);

namespace App\Domain\Admin\Transformer;

use App\Domain\Admin\Dictionary\DuplicationTargetOptionDictionary;
use App\Domain\Admin\DTO\DuplicationFormDTO;
use App\Domain\Admin\Hydrator\DuplicationHydrator;
use App\Domain\Admin\Model\Duplication;
use App\Domain\User\Repository as UserRepository;

class DuplicationFormDTOTransformer
{
    /**
     * @var UserRepository\Collectivity
     */
    private $collectivityRepository;

    /**
     * @var DuplicationHydrator
     */
    private $hydrator;

    /**
     * DuplicationDTOTransformer constructor.
     *
     * @param UserRepository\Collectivity $collectivityRepository
     * @param DuplicationHydrator         $hydrator
     */
    public function __construct(
        UserRepository\Collectivity $collectivityRepository,
        DuplicationHydrator $hydrator
    ) {
        $this->collectivityRepository = $collectivityRepository;
        $this->hydrator               = $hydrator;
    }

    /**
     * Transform a DuplicationFormDTO to a Duplication object model.
     *
     * @param DuplicationFormDTO $formDTO
     *
     * @throws \Exception
     *
     * @return Duplication
     */
    public function toModelObject(DuplicationFormDTO $formDTO): Duplication
    {
        $model = new Duplication(
            $formDTO->getType(),
            $formDTO->getSourceCollectivity(),
            []
        );

        switch ($formDTO->getTargetOption()) {
            case DuplicationTargetOptionDictionary::KEY_PER_COLLECTIVITY:
                $this->addTargetCollectivitiesPerCollectivity($formDTO, $model);
                break;
            case DuplicationTargetOptionDictionary::KEY_PER_TYPE:
                $this->addTargetCollectivitiesPerType($formDTO, $model);
                break;
            default:
                throw new \RuntimeException('Unable to read target collectivities for provided target option ' . $formDTO->getTargetOption());
        }

        foreach ($formDTO->getData() as $data) {
            $model->addDataId($data);
        }

        $this->hydrator->hydrate($model);

        return $model;
    }

    /**
     * Add target collectivities formatted from "PER COLLECTIVITY" target options.
     *
     * @param DuplicationFormDTO $formDTO
     * @param Duplication        $model
     */
    protected function addTargetCollectivitiesPerCollectivity(DuplicationFormDTO $formDTO, Duplication $model): void
    {
        foreach ($formDTO->getTargetCollectivities() as $collectivity) {
            $model->addTargetCollectivity($collectivity);
        }
    }

    /**
     * Add target collectivities formatted from "PER TYPE" target options.
     *
     * @param DuplicationFormDTO $formDTO
     * @param Duplication        $model
     */
    protected function addTargetCollectivitiesPerType(DuplicationFormDTO $formDTO, Duplication $model): void
    {
        $collectivities = $this->collectivityRepository->findByTypes(
            $formDTO->getTargetCollectivityTypes(),
            $formDTO->getSourceCollectivity()
        );

        foreach ($collectivities as $collectivity) {
            $model->addTargetCollectivity($collectivity);
        }
    }
}
