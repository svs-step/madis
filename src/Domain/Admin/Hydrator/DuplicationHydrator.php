<?php

declare(strict_types=1);

namespace App\Domain\Admin\Hydrator;

use App\Domain\Admin\Dictionary\DuplicationTypeDictionary;
use App\Domain\Admin\Model;
use App\Domain\Admin\Model\Duplication;
use App\Domain\Registry\Repository as RegistryRepository;

class DuplicationHydrator
{
    /**
     * @var RegistryRepository\Treatment
     */
    private $treatmentRepository;

    /**
     * @var RegistryRepository\Contractor
     */
    private $contractorRepository;

    /**
     * @var RegistryRepository\Mesurement
     */
    private $mesurementRepository;

    /**
     * DuplicationDTOTransformer constructor.
     *
     * @param RegistryRepository\Treatment  $treatmentRepository
     * @param RegistryRepository\Contractor $contractorRepository
     * @param RegistryRepository\Mesurement $mesurementRepository
     */
    public function __construct(
        RegistryRepository\Treatment $treatmentRepository,
        RegistryRepository\Contractor $contractorRepository,
        RegistryRepository\Mesurement $mesurementRepository
    ) {
        $this->treatmentRepository  = $treatmentRepository;
        $this->contractorRepository = $contractorRepository;
        $this->mesurementRepository = $mesurementRepository;
    }

    /**
     * Hydrate a Duplication Model thanks to it related fields.
     *
     * @param Duplication $duplication
     *
     * @return Duplication
     */
    public function hydrate(Model\Duplication $duplication): Model\Duplication
    {
        $this->hydrateDataField($duplication);

        return $duplication;
    }

    /**
     * Add every data ids (converted as object) from DuplicationFormDTO to Duplication.
     *
     * @param Duplication $model
     */
    protected function hydrateDataField(Duplication $model): void
    {
        switch ($model->getType()) {
            case DuplicationTypeDictionary::KEY_TREATMENT:
                $repository = $this->treatmentRepository;
                break;
            case DuplicationTypeDictionary::KEY_CONTRACTOR:
                $repository = $this->contractorRepository;
                break;
            case DuplicationTypeDictionary::KEY_MESUREMENT:
                $repository = $this->mesurementRepository;
                break;
            default:
                throw new \RuntimeException('Data object type is not supported');
        }

        foreach ($model->getDataIds() as $dataId) {
            $model->addData($repository->findOneById($dataId));
        }
    }
}
