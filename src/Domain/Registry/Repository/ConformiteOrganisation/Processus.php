<?php

namespace App\Domain\Registry\Repository\ConformiteOrganisation;

use App\Application\DDD\Repository\CRUDRepositoryInterface;
use App\Domain\Registry\Model as Model;

interface Processus extends CRUDRepositoryInterface
{
    public function findAllWithQuestions();

    public function findOneByPosition(int $position);

    public function findNewNotUsedInGivenConformite(Model\ConformiteOrganisation\Evaluation $evaluation);
}
