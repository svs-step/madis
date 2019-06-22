<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan@awkan.fr>
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

class Contractor extends CRUDRepository implements Repository\Contractor
{
    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\Contractor::class;
    }

    /**
     * {@inheritdoc}
     */
    public function findAllByCollectivity(Collectivity $collectivity, array $order = [])
    {
        $qb = $this->createQueryBuilder()
            ->andWhere('o.collectivity = :collectivity')
            ->setParameter('collectivity', $collectivity)
        ;

        foreach ($order as $key => $dir) {
            $qb->addOrderBy("o.{$key}", $dir);
        }

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }
}
