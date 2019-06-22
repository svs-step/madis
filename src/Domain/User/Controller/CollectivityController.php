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
use App\Domain\User\Form\Type\CollectivityType;
use App\Domain\User\Model;
use App\Domain\User\Repository;
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

    /**
     * {@inheritdoc}
     */
    protected function getDomain(): string
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModel(): string
    {
        return 'collectivity';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\Collectivity::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormType(): string
    {
        return CollectivityType::class;
    }
}
