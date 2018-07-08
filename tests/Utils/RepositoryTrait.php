<?php

declare(strict_types=1);

namespace App\Tests\Utils;

use App\Domain\User\Model\Collectivity;

trait RepositoryTrait
{
    private function addCollectivityClause($qb, Collectivity $collectivity)
    {
        $qb
            ->andWhere('o.collectivity = :collectivity')
            ->shouldBeCalled()
            ->willReturn($qb)
        ;
        $qb
            ->setParameter('collectivity', $collectivity)
            ->shouldBeCalled()
            ->willReturn($qb)
        ;

        return $qb;
    }

    private function addActiveClause($qb, bool $active = true)
    {
        $qb
            ->andWhere('o.active = :active')
            ->shouldBeCalled()
            ->willReturn($qb)
        ;
        $qb
            ->setParameter('active', $active)
            ->shouldBeCalled()
            ->willReturn($qb)
        ;

        return $qb;
    }

    private function addOrderClause($qb, string $key, string $dir)
    {
        $qb
            ->addOrderBy("o.{$key}", $dir)
            ->shouldBeCalled()
            ->willReturn($qb)
        ;
    }
}
