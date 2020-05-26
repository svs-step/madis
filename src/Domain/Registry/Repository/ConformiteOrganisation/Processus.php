<?php

namespace App\Domain\Registry\Repository\ConformiteOrganisation;

use App\Application\DDD\Repository\CRUDRepositoryInterface;

interface Processus extends CRUDRepositoryInterface
{
    public function findAllWithQuestions();

    public function findOneByPosition(int $position);
}
