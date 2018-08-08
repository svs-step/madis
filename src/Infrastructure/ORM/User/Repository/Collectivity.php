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

namespace App\Infrastructure\ORM\User\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\User\Model;
use App\Domain\User\Repository;

class Collectivity extends CRUDRepository implements Repository\Collectivity
{
    protected function getModelClass(): string
    {
        return Model\Collectivity::class;
    }
}
