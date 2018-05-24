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

namespace App\Domain\User\Controller;

use App\Application\Controller\CRUDController;
use App\Domain\User\Form\Type\UserType;
use App\Domain\User\Model\User;

class UserController extends CRUDController
{
    protected function getDomain(): string
    {
        return 'user';
    }

    protected function getModel(): string
    {
        return 'user';
    }

    protected function getModelClass(): string
    {
        return User::class;
    }

    protected function getFormType(): string
    {
        return UserType::class;
    }
}
