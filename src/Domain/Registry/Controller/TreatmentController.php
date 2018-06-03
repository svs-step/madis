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

namespace App\Domain\Registry\Controller;

use App\Application\Controller\CRUDController;
use App\Domain\Registry\Form\Type\TreatmentType;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class TreatmentController extends CRUDController
{
    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\Treatment $repository
    ) {
        parent::__construct($entityManager, $translator, $repository);
    }

    protected function getDomain(): string
    {
        return 'registry';
    }

    protected function getModel(): string
    {
        return 'treatment';
    }

    protected function getModelClass(): string
    {
        return Model\Treatment::class;
    }

    protected function getFormType(): string
    {
        return TreatmentType::class;
    }
}
