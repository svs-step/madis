<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Repository;

use App\Application\DDD\Repository\CRUDRepositoryInterface;
use App\Application\Doctrine\Repository\DataTablesRepository;
use App\Domain\User\Repository\Collectivity;

interface ModeleAnalyse extends CRUDRepositoryInterface, DataTablesRepository
{
    public function findAllByCollectivity(Collectivity $collectivity);
}
