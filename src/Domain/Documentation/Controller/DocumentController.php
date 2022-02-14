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
use App\Domain\User\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Gaufrette\Exception\FileNotFound;
use Gaufrette\FilesystemInterface;
use Knp\Snappy\Pdf;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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

    /**
     * @var FilesystemInterface
     */
    protected $documentFilesystem;

    /**
     * @var FilesystemInterface
     */
    protected $thumbFilesystem;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var Repository\Category
     */
    protected $categoryRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\Document $repository,
        Repository\Category $categoryRepository,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProvider $userProvider,
        FilesystemInterface $documentFilesystem,
        FilesystemInterface $thumbFilesystem,
        Pdf $pdf,
        RequestStack $requestStack
    ) {
        parent::__construct($entityManager, $translator, $repository, $pdf, $userProvider, $authorizationChecker);
        $this->authorizationChecker           = $authorizationChecker;
        $this->documentFilesystem             = $documentFilesystem;
        $this->thumbFilesystem                = $thumbFilesystem;
        $this->requestStack                   = $requestStack;
        $this->categoryRepository             = $categoryRepository;
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
            'pinned'    => 'DESC',
            'createdAt' => 'DESC',
        ];

        // Everybody can access all documents
        return $this->repository->findAll($order);
    }

    public function indexAction()
    {
        $user = $this->getUser();
        if ($user->isDocumentView()) {
            return $this->gridAction();
        }

        return $this->listAction();
    }

    /**
     * Trigger document file download.
     *
     * @return BinaryFileResponse
     */
    public function downloadAction(string $name)
    {
        $doc = $this->repository->findOneByName($name);
        if (!$doc) {
            throw new NotFoundHttpException('Document introuvable');
        }

        $fileStream = sprintf('gaufrette://documentation_document/%s', $doc->getFile());

        $response = new BinaryFileResponse($fileStream);
        $mimeType = $this->documentFilesystem->mimeType($doc->getFile());
        $ext      = pathinfo($doc->getFile(), PATHINFO_EXTENSION);
        $response->headers->set('Content-Type', $mimeType);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $doc->getName() . '.' . $ext
        );

        return $response;
    }

    public function deleteConfirmationAction(string $id): Response
    {
        $doc = $this->repository->findOneByID($id);
        if (!$doc) {
            throw new NotFoundHttpException('Document introuvable');
        }
        if ($doc->getFile()) {
            try {
                $this->documentFilesystem->delete($doc->getFile());
            } catch (FileNotFound $e) {
                // File does not exist, do nothing
            }
        }

        return parent::deleteConfirmationAction($id); // TODO: Change the autogenerated stub
    }

    /*
     * The list action view
     * Get data & display them.
     */
    public function listAction(): Response
    {
        // Set default view to list for current user
        $user = $this->getUser();
        $user->setDocumentView(false);
        $this->entityManager->flush();

        $categories = $this
        ->getDoctrine()
        ->getRepository(Model\Category::class)
        ->findAll();

        return $this->render($this->getTemplatingBasePath('list'), [
            'objects'    => $this->getListData(),
            'categories' => $categories,
        ]);
    }

    public function gridAction(): Response
    {
        // Set default view to list for current user
        $user = $this->getUser();
        $user->setDocumentView(true);
        $this->entityManager->flush();

        $categories = $this
        ->getDoctrine()
        ->getRepository(Model\Category::class)
        ->findAll();

        return $this->render($this->getTemplatingBasePath('grid'), [
            'objects'    => $this->getListData(),
            'categories' => $categories,
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @param Model\Document $object
     */
    public function formPrePersistData($object)
    {
        if (false === $object->getIsLink() && null !== $file = $object->getUploadedFile()) {
            $filename = Uuid::uuid4()->toString() . '.' . $file->getClientOriginalExtension();
            $this->documentFilesystem->write($filename, \fopen($file->getRealPath(), 'r'));
            $size = $this->documentFilesystem->size($filename);

            $object->setSize($size);

            $object->setFile($filename);
            $object->setUploadedFile(null);

            $url = $this->generateUrl('documentation_document_download', [
                'name' => $filename,
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            $object->setUrl($url);
        } elseif (true === $object->getIsLink()) {
            $object->setFile('');
            $object->setSize(0);
        }
        if (null !== $thumb = $object->getThumbUploadedFile()) {
            $filename = Uuid::uuid4()->toString() . '.' . $thumb->getClientOriginalExtension();
            $this->thumbFilesystem->write($filename, \fopen($thumb->getRealPath(), 'r'));

            $object->setThumbUploadedFile(null);
            $object->setThumbUrl('/uploads/documentation/vignettes/' . $filename);
        }
    }

    /**
     * Trigger download or redirect to link when a user open a share link.
     */
    public function shareAction(string $id)
    {
        $doc = $this->repository->findOneByID($id);
        if (!$doc) {
            throw new NotFoundHttpException('Document introuvable');
        }

        if ($doc->getIsLink()) {
            return $this->redirect($doc->getUrl());
        }

        return $this->downloadAction($doc->getFile());
    }

    /**
     * Mark this document as favorite for the current user.
     */
    public function favoriteAction(Request $request, string $id)
    {
        /**
         * @var Model\Document
         */
        $doc = $this->repository->findOneByID($id);
        /**
         * @var User
         */
        $user = $this->getUser();
        if (!$doc) {
            throw new NotFoundHttpException('Document introuvable');
        }

        $favorited = $user->getFavoriteDocuments();

        // Is the current document already favorited ?
        if ($favorited->contains($doc)) {
            // If so, remove it
            $favorited->removeElement($doc);
            $doc->removeFavoritedUser($user);
        } else {
            // Other wise, add it
            $favorited->add($doc);
            $doc->addFavoritedUser($user);
        }

        $user->setFavoriteDocuments($favorited);

        $this->entityManager->persist($user);
        $this->entityManager->persist($doc);
        $this->entityManager->flush();

        $this->getDoctrine()->getManagerForClass(User::class)->flush();

        return $this->redirect($request->get('back'));
    }
}
