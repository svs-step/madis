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

namespace App\Domain\Admin\Controller;

use App\Application\Controller\CRUDController;
use App\Domain\Admin\Form\Type\CollectivityType;
use App\Domain\Admin\Model\Collectivity;

class CollectivityController extends CRUDController
{
    protected function getDomain(): string
    {
        return 'admin';
    }

    protected function getModel(): string
    {
        return 'collectivity';
    }

    protected function getModelClass(): string
    {
        return Collectivity::class;
    }

    protected function getFormType(): string
    {
        return CollectivityType::class;
    }
}
