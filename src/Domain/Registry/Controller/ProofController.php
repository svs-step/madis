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

namespace App\Domain\Registry\Controller;

use App\Application\Controller\CRUDController;
use App\Application\Symfony\Security\UserProvider;
use App\Domain\Registry\Form\Type\ProofType;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\Reporting\Handler\WordHandler;
use Doctrine\ORM\EntityManagerInterface;
use Gaufrette\FilesystemInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Exception\MethodNotImplementedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property Repository\Proof $repository
 */
class ProofController extends CRUDController
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var WordHandler
     */
    protected $wordHandler;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var UserProvider
     */
    protected $userProvider;

    /**
     * @var FilesystemInterface
     */
    protected $documentFilesystem;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\Proof $repository,
        RequestStack $requestStack,
        WordHandler $wordHandler,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProvider $userProvider,
        FilesystemInterface $documentFilesystem
    ) {
        parent::__construct($entityManager, $translator, $repository);
        $this->requestStack         = $requestStack;
        $this->wordHandler          = $wordHandler;
        $this->authorizationChecker = $authorizationChecker;
        $this->userProvider         = $userProvider;
        $this->documentFilesystem   = $documentFilesystem;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDomain(): string
    {
        return 'registry';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModel(): string
    {
        return 'proof';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\Proof::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormType(): string
    {
        return ProofType::class;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    protected function getListData()
    {
        $request   = $this->requestStack->getMasterRequest();
        $archived  = 'true' === $request->query->get('archive') ? true : false;

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->repository->findAllArchived($archived);
        }

        return $this->repository->findAllArchivedByCollectivity(
            $this->userProvider->getAuthenticatedUser()->getCollectivity(),
            $archived
        );
    }

    /**
     * {@inheritdoc}
     * - Upload documentFile before object persistence in database.
     *
     * @throws \Exception
     */
    public function formPrePersistData($object)
    {
        if (!$object instanceof Model\Proof) {
            throw new \RuntimeException('You must persist a ' . Model\Proof::class . ' object class with your form');
        }

        $file = $object->getDocumentFile();

        if ($file) {
            $filename = Uuid::uuid4()->toString() . '.' . $file->getClientOriginalExtension();
            $this->documentFilesystem->write($filename, \fopen($file->getRealPath(), 'r'));
            $object->setDocument($filename);
            $object->setDocumentFile(null);
        }
    }

    /**
     * The archive action view
     * Display a confirmation message to confirm data archivage.
     *
     * @param string $id
     *
     * @return Response
     */
    public function archiveAction(string $id): Response
    {
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        return $this->render($this->getTemplatingBasePath('archive'), [
            'object' => $object,
        ]);
    }

    /**
     * The archive action
     * Archive the data.
     *
     * @param string $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function archiveConfirmationAction(string $id): Response
    {
        /** @var Model\Proof|null $object */
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        $object->setDeletedAt(new \DateTimeImmutable());
        $this->repository->update($object);

        $this->addFlash('success', $this->getFlashbagMessage('success', 'archive', $object));

        return $this->redirectToRoute($this->getRouteName('list'));
    }

    /**
     * {@inheritdoc}
     * OVERRIDE METHOD.
     * Override deletion in order to delete Proof into server.
     */
    public function deleteConfirmationAction(string $id): Response
    {
        /** @var Model\Proof|null $object */
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        if ($this->isSoftDelete()) {
            if (!\method_exists($object, 'setDeletedAt')) {
                throw new MethodNotImplementedException('setDeletedAt');
            }
            $object->setDeletedAt(new \DateTimeImmutable());
            $this->repository->update($object);
        } else {
            $filename = $object->getDocument();

            $this->entityManager->remove($object);
            $this->entityManager->flush();

            // TODO: Log error if deletion fail
            $this->documentFilesystem->delete($filename);
        }

        $this->addFlash('success', $this->getFlashbagMessage('success', 'delete', $object));

        return $this->redirectToRoute($this->getRouteName('list'));
    }

    /**
     * Download uploaded document which belongs to provided object id.
     *
     * @param string $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function downloadAction(string $id): Response
    {
        /** @var Model\Proof|null $object */
        $object = $this->repository->findOneById($id);

        if (!$object) {
            throw new NotFoundHttpException("No object exists with id '{$id}'");
        }

        // Can only download if we belong to the collectivity or if we are admin
        if (
            $this->userProvider->getAuthenticatedUser()->getCollectivity() !== $object->getCollectivity()
            && !$this->authorizationChecker->isGranted('ROLE_ADMIN')
        ) {
            throw new AccessDeniedHttpException("You can't access to an object that does not belong to your collectivity");
        }

        $extension = \pathinfo($object->getDocument(), PATHINFO_EXTENSION);
        $response  = new BinaryFileResponse('gaufrette://registry_proof_document/' . $object->getDocument());

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            "{$object->getName()}.{$extension}"
        );

        return $response;
    }
}
