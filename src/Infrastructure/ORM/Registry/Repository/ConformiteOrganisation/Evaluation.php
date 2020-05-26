<?php

namespace App\Infrastructure\ORM\Registry\Repository\ConformiteOrganisation;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;

class Evaluation extends CRUDRepository implements Repository\ConformiteOrganisation\Evaluation
{
    protected function getModelClass(): string
    {
        return Model\ConformiteOrganisation\Evaluation::class;
    }
}
