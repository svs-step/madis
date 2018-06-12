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

namespace App\Application\Traits\Model;

use App\Domain\User\Model\Collectivity;

trait CollectivityTrait
{
    /**
     * @var Collectivity
     */
    private $collectivity;

    /**
     * @return Collectivity|null
     */
    public function getCollectivity(): ?Collectivity
    {
        return $this->collectivity;
    }

    /**
     * @param Collectivity $collectivity
     */
    public function setCollectivity(Collectivity $collectivity): void
    {
        $this->collectivity = $collectivity;
    }
}
