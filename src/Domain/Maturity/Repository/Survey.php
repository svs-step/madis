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

namespace App\Domain\Maturity\Repository;

use App\Application\DDD\Repository\CRUDRepositoryInterface;
use App\Domain\User\Model\Collectivity;

interface Survey extends CRUDRepositoryInterface
{
    /**
     * Find all survey by associated collectivity.
     *
     * @param Collectivity $collectivity The collectivity to search with
     * @param array        $order        Order the data
     * @param int          $limit
     *
     * @return array The array of survey given by the collectivity
     */
    public function findAllByCollectivity(Collectivity $collectivity, array $order = [], int $limit = null): iterable;

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
