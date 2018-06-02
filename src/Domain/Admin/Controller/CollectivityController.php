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
use App\Domain\Admin\Model;
use App\Domain\Admin\Repository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class CollectivityController extends CRUDController
{
    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\Collectivity $repository
    ) {
        parent::__construct($entityManager, $translator, $repository);
    }

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
        return Model\Collectivity::class;
    }

    protected function getFormType(): string
    {
        return CollectivityType::class;
    }
}
