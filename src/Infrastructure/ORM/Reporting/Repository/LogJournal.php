<?php

declare(strict_types=1);

namespace App\Infrastructure\ORM\Reporting\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\Reporting\Model;
use App\Domain\Reporting\Repository;

class LogJournal extends CRUDRepository implements Repository\LogJournal
{
    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\LogJournal::class;
    }
}
