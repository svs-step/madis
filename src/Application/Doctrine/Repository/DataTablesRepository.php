<?php

namespace App\Application\Doctrine\Repository;

interface DataTablesRepository
{
    public function count(array $criteria = []);

    public function findPaginated($firstResult, $maxResults, $orderColumn, $orderDir, $searches, $criteria = []);
}
