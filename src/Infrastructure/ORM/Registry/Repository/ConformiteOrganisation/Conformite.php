<?php

namespace App\Infrastructure\ORM\Registry\Repository\ConformiteOrganisation;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\Registry\Model\ConformiteOrganisation as Model;
use App\Domain\Registry\Repository\ConformiteOrganisation as Repository;

class Conformite extends CRUDRepository implements Repository\Conformite
{
    protected function getModelClass(): string
    {
        return Model\Conformite::class;
    }
}
