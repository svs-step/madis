<?php

namespace App\Application\Doctrine\Repository;

interface DataTablesRepository
{
    public function count();

    public function findPaginated($firstResult, $maxResults, $orderColumn, $orderDir, $searches);
}
