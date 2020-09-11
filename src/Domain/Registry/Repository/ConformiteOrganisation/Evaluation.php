<?php

namespace App\Domain\Registry\Repository\ConformiteOrganisation;

use App\Application\DDD\Repository\CRUDRepositoryInterface;
use App\Domain\User\Model\Collectivity;

interface Evaluation extends CRUDRepositoryInterface
{
    /**
     * @param Collectivity|array|null $organisation
     *
     * @return mixed
     */
    public function findAllByActiveOrganisationWithHasModuleConformiteOrganisationAndOrderedByDate($organisation = null);

    public function findLastByOrganisation(Collectivity $organisation): ?\App\Domain\Registry\Model\ConformiteOrganisation\Evaluation;
}
