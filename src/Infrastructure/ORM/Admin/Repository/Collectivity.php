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

namespace App\Infrastructure\ORM\Admin\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\Admin\Model;
use App\Domain\Admin\Repository;

class Collectivity extends CRUDRepository implements Repository\Collectivity
{
    protected function getModelClass(): string
    {
        return Model\Collectivity::class;
    }
}
