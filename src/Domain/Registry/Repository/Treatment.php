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

namespace App\Domain\Registry\Repository;

use App\Application\DDD\Repository\CRUDRepositoryInterface;
use App\Domain\User\Model\Collectivity;

interface Treatment extends CRUDRepositoryInterface
{
    /**
     * Find all treatments by associated collectivity.
     *
     * @param Collectivity $collectivity The collectivity to search with
     *
     * @return array The array of treatments given by the collectivity
     */
    public function findAllByCollectivity(Collectivity $collectivity);
}
