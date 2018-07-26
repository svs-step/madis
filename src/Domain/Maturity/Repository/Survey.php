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

namespace App\Domain\Maturity\Repository;

use App\Application\DDD\Repository\CRUDRepositoryInterface;

interface Survey extends CRUDRepositoryInterface
{
    /**
     * Find previous survey by created_at date.
     *
     * @param string $id
     * @param int    $limit
     *
     * @return iterable
     */
    public function findPreviousById(string $id, int $limit = 1): iterable;
}
