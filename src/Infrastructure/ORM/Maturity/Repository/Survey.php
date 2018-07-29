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

namespace App\Infrastructure\ORM\Maturity\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\Maturity\Model;
use App\Domain\Maturity\Repository;
use App\Domain\User\Model\Collectivity;

class Survey extends CRUDRepository implements Repository\Survey
{
    protected function getModelClass(): string
    {
        return Model\Survey::class;
    }

    /**
     * {@inheritdoc}
     */
    public function findAllByCollectivity(Collectivity $collectivity, array $order = []): iterable
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

    /**
     * {@inheritdoc}
     */
    public function findPreviousById(string $id, int $limit = 1): iterable
    {
        return $this->createQueryBuilder()
            ->select('s')
            ->from(Model\Survey::class, 's')
            ->andWhere('o.id = :id')
            ->andWhere('o.collectivity = s.collectivity')
            ->andWhere('o.createdAt > s.createdAt')
            ->orderBy('s.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()
        ;
    }
}
