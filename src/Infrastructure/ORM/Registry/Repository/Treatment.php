<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan.bourlard@outlook.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Infrastructure\ORM\Registry\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\User\Model\Collectivity;

class Treatment extends CRUDRepository implements Repository\Treatment
{
    protected function getModelClass(): string
    {
        return Model\Treatment::class;
    }

    /**
     * Find all treatments by associated collectivity.
     *
     * @param Collectivity $collectivity The collectivity to search with
     *
     * @return array The array of treatments given by the collectivity
     */
    public function findAllByCollectivity(Collectivity $collectivity)
    {
        return $this->createQueryBuilder()
            ->andWhere('o.collectivity = :collectivity')
            ->setParameter('collectivity', $collectivity)
            ->getQuery()
            ->getResult()
        ;
    }
}
