<?php

namespace App\Domain\Registry\Repository\ConformiteOrganisation;

use App\Application\DDD\Repository\CRUDRepositoryInterface;

interface Evaluation extends CRUDRepositoryInterface
{
    public function findAllByOrganisationOrderedByDate(string $idOrganisation = null);
}
