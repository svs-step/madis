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

namespace App\Domain\User\Controller;

use App\Application\Controller\CRUDController;
use App\Domain\User\Form\Type\UserType;
use App\Domain\User\Model;
use App\Domain\User\Repository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class UserController extends CRUDController
{
    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\User $repository
    ) {
        parent::__construct($entityManager, $translator, $repository);
    }

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
        return Model\User::class;
    }

    protected function getFormType(): string
    {
        return UserType::class;
    }
}
