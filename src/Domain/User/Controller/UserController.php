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

namespace App\Domain\User\Controller;

use App\Application\Controller\CRUDController;
use App\Domain\User\Form\Type\UserType;
use App\Domain\User\Model;
use App\Domain\User\Repository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Exception\MethodNotImplementedException;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property Repository\User $repository
 */
class UserController extends CRUDController
{
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\User $repository,
        RequestStack $requestStack,
        EncoderFactoryInterface $encoderFactory
    ) {
        parent::__construct($entityManager, $translator, $repository);
        $this->requestStack   = $requestStack;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function getListData(): iterable
    {
        $request   = $this->requestStack->getMasterRequest();
        $archived  = 'true' === $request->query->get('archive') ? true : false;

        return $this->repository->findAllArchived($archived);
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
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\User::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormType(): string
    {
        return UserType::class;
    }

    protected function isSoftDelete(): bool
    {
        return true;
    }

    /**
     * The unarchive action view
     * Display a confirmation message to confirm data un-archivage.
     *
     * @param string $id
     *
     * @return Response
     */
    public function unarchiveAction(string $id): Response
    {
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        return $this->render($this->getTemplatingBasePath('unarchive'), [
            'object' => $object,
        ]);
    }

    /**
     * The unarchive action
     * Unarchive the data.
     *
     * @param string $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function unarchiveConfirmationAction(string $id): Response
    {
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        if (!\method_exists($object, 'setDeletedAt')) {
            throw new MethodNotImplementedException('setDeletedAt');
        }
        $object->setDeletedAt(null);
        $this->repository->update($object);

        $this->addFlash('success', $this->getFlashbagMessage('success', 'unarchive', $object));

        return $this->redirectToRoute($this->getRouteName('list'));
    }
}
