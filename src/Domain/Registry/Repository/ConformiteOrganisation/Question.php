<?php

namespace App\Domain\Registry\Repository\ConformiteOrganisation;

use App\Application\DDD\Repository\CRUDRepositoryInterface;
use App\Domain\Registry\Model\ConformiteOrganisation\Conformite;
use App\Domain\Registry\Model\ConformiteOrganisation\Processus;

interface Question extends CRUDRepositoryInterface
{
    public function findAllByProcessus(Processus $processus);

    public function findNewNotUsedByGivenConformite(Conformite $conformite);
}
