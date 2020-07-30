<?php

namespace App\Domain\Registry\Repository\ConformiteOrganisation;

use App\Application\DDD\Repository\CRUDRepositoryInterface;
use App\Domain\User\Model\Collectivity;

interface Evaluation extends CRUDRepositoryInterface
{
    public function findAllByActiveOrganisationWhithHasModuleConformiteOrganisationAndOrderedByDate(Collectivity $organisation = null);

    public function findLastByOrganisation(Collectivity $organisation): ?\App\Domain\Registry\Model\ConformiteOrganisation\Evaluation;
}
