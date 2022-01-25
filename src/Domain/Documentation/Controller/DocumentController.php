<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author Donovan Bourlard <donovan@awkan.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace App\Domain\Documentation\Controller;

use App\Application\Controller\CRUDController;
use App\Application\Symfony\Security\UserProvider;
use App\Domain\Documentation\Form\Type\DocumentType;
use App\Domain\Documentation\Model;
use App\Domain\Documentation\Repository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property Repository\Document $repository
 */
class DocumentController extends CRUDController
{
    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var UserProvider
     */
    protected $userProvider;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\Document $repository,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProvider $userProvider,
        Pdf $pdf
    ) {
        parent::__construct($entityManager, $translator, $repository, $pdf, $userProvider, $authorizationChecker);
        $this->authorizationChecker = $authorizationChecker;
        $this->userProvider         = $userProvider;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDomain(): string
    {
        return 'documentation';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModel(): string
    {
        return 'document';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\Document::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormType(): string
    {
        return DocumentType::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getListData()
    {
        $order = [
            'createdAt' => 'DESC',
        ];

        // Everybody can access all documents
        return $this->repository->findAll($order);
    }
}