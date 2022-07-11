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
use App\Domain\Documentation\Form\Type\CategoryType;
use App\Domain\Documentation\Model;
use App\Domain\Documentation\Repository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Exception\MethodNotImplementedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property Repository\Category $repository
 */
class CategoryController extends CRUDController
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
        Repository\Category $repository,
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
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\Category::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormType(): string
    {
        return CategoryType::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getListData()
    {
        $order = [
            'createdAt' => 'DESC',
        ];

        return $this->repository->findAll($order);
    }

    /**
     * {@inheritdoc}
     * Here, we wanna compute maturity score.
     *
     * @param Model\Category $object
     */
    public function formPrePersistData($object)
    {
        $object->setSysteme(false);
    }

    /**
     * The delete action view
     * Display a confirmation message to confirm data deletion.
     *
     * @Override
     */
    public function deleteAction(string $id): Response
    {
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $this->addFlash('success', $this->getFlashbagMessage('success', 'delete', $object));

            return $this->redirectToRoute('documentation_document_list');
        }

        return $this->render($this->getTemplatingBasePath('delete'), [
            'object'            => $object,
        ]);
    }

    /**
     * The deletion action
     * Delete the data.
     *
     * @throws \Exception
     */
    public function deleteConfirmationAction(string $id): Response
    {
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN') || $object->getSysteme()) {
            $this->addFlash('success', $this->getFlashbagMessage('error', 'delete', $object));

            return $this->redirectToRoute('documentation_document_list');
        }

        if ($this->isSoftDelete()) {
            if (!\method_exists($object, 'setDeletedAt')) {
                throw new MethodNotImplementedException('setDeletedAt');
            }
            $object->setDeletedAt(new \DateTimeImmutable());
            $this->repository->update($object);
        } else {
            $this->entityManager->remove($object);
            $this->entityManager->flush();
        }

        $this->addFlash('success', $this->getFlashbagMessage('success', 'delete', $object));

        return $this->redirectToRoute($this->getRouteName('list'));
    }
}
