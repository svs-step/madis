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

interface Mesurement extends CRUDRepositoryInterface
{
    /**
     * Find all mesurements by associated collectivity.
     *
     * @param Collectivity $collectivity The collectivity to search with
     * @param array        $order        Order results
     *
     * @return array The array of mesurements given by the collectivity
     */
    public function findAllByCollectivity(Collectivity $collectivity, array $order = []);

    /**
     * Find all mesurements by criteria.
     *
     * @param array $criteria List of criteria
     *
     * @return array The array of mesurements given by criteria
     */
    public function findBy(array $criteria = []);

    /**
     * Find all planified mesurements by criteria.
     *
     * @param array $criteria List of criteria
     *
     * @return array The array of mesurements given by criteria
     */
    public function findByPlanified(array $criteria = []);
}
