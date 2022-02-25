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
use App\Application\Traits\ServersideDatatablesTrait;
use App\Domain\Documentation\Model\Category;
use App\Domain\Documentation\Model\Document;
use App\Domain\Registry\Dictionary\ProofTypeDictionary;
use App\Domain\Registry\Form\Type\ProofType;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\Reporting\Handler\WordHandler;
use App\Domain\Tools\ChainManipulator;
use App\Domain\User\Dictionary\UserRoleDictionary;
use Doctrine\ORM\EntityManagerInterface;
use Gaufrette\Filesystem;
use Gaufrette\FilesystemInterface;
use Knp\Snappy\Pdf;
use PhpOffice\PhpWord\Shared\ZipArchive;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Exception\MethodNotImplementedException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;


/**
 * @property Repository\Proof $repository
 */
class ProofController extends CRUDController
{
    use ServersideDatatablesTrait;

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

    /**
     * @var RouterInterface
     */
    protected $router;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\Proof $repository,
        RequestStack $requestStack,
        WordHandler $wordHandler,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProvider $userProvider,
        FilesystemInterface $documentFilesystem,
        Pdf $pdf,
        RouterInterface $router
    ) {
        parent::__construct($entityManager, $translator, $repository, $pdf, $userProvider, $authorizationChecker);
        $this->requestStack         = $requestStack;
        $this->wordHandler          = $wordHandler;
        $this->authorizationChecker = $authorizationChecker;
        $this->userProvider         = $userProvider;
        $this->documentFilesystem   = $documentFilesystem;
        $this->router               = $router;
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
     * @throws \Exception
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
     * @throws \Exception
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

        $filenameWithoutAccent = ChainManipulator::removeAccents($object->getName());
        $filename              = ChainManipulator::removeAllNonAlphaNumericChar($filenameWithoutAccent);

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            "{$filename}.{$extension}"
        );

        return $response;
    }

    public function downloadAll()
    {

        /** @var Model\Proof|null $object */
        $objects = $this->repository->findAll();

        $files = [];
        foreach ($objects as $object){
            if (!$object->getDeletedAt()){
                $files[] = $object->getDocument();
            }
        }
        $zip =  new ZipArchive();
        $filename = "./uploads/registry/proof/zip/test.zip";


        if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
            exit("Impossible d'ouvrir le fichier $filename>\n");
        }

        foreach ($files as $file){
            $zip->addFile('./uploads/registry/proof/document/'.$file, $file);
        }

        $zip->close();

        $date = date('dmY');
        $response = new Response(file_get_contents($filename));
        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', 'attachment;filename="Documents'.$date.'.zip"');
        $response->headers->set('Content-length', filesize($filename));

        return $response;

    }

    public function listAction(): Response
    {
        $criteria = $this->getRequestCriteria();

        $category = $this->entityManager->getRepository(Category::class)->findOneBy([
            'name' => 'Preuves',
        ]);

        return $this->render($this->getTemplatingBasePath('list'), [
            'totalItem'   => $this->repository->count($criteria),
            'category'    => $category,
            'route'       => $this->router->generate('registry_proof_list_datatables', ['archive' => $criteria['archive']]),
        ]);
    }

    public function listDataTables(Request $request): JsonResponse
    {
        $criteria    = $this->getRequestCriteria();
        $users       = $this->getResults($request, $criteria);
        $reponse     = $this->getBaseDataTablesResponse($request, $users, $criteria);

        /** @var Model\Proof $proof */
        foreach ($users as $proof) {
            $reponse['data'][] = [
                'nom'          => $proof->getName(),
                'collectivite' => $proof->getCollectivity()->getName(),
                'type'         => !\is_null($proof->getType()) ? ProofTypeDictionary::getTypes()[$proof->getType()] : null,
                'commentaire'  => $proof->getComment(),
                'date'         => \date_format($proof->getCreatedAt(), 'd/m/Y H:i'),
                'actions'      => $this->getActionCellsContent($proof),
            ];
        }

        $jsonResponse = new JsonResponse();
        $jsonResponse->setJson(\json_encode($reponse));

        return $jsonResponse;
    }

    protected function getLabelAndKeysArray(): array
    {
        return [
            0 => 'nom',
            1 => 'collectivite',
            2 => 'type',
            3 => 'commentaire',
            4 => 'date',
            5 => 'actions',
        ];
    }

    private function getRequestCriteria()
    {
        $criteria            = [];
        $request             = $this->requestStack->getMasterRequest();
        $criteria['archive'] = $request->query->getBoolean('archive');
        $user                = $this->userProvider->getAuthenticatedUser();

        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $criteria['collectivity'] = $user->getCollectivity();
        }

        if (\in_array(UserRoleDictionary::ROLE_REFERENT, $user->getRoles())) {
            $criteria['collectivity'] = $user->getCollectivitesReferees();
        }

        return $criteria;
    }

    private function getActionCellsContent(Model\Proof $proof)
    {
        $cellContent = '';
        if ($this->userProvider->getAuthenticatedUser()->getCollectivity() === $proof->getCollectivity()
            || $this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $cellContent .= '<a href="' . $this->router->generate('registry_proof_download', ['id' => $proof->getId()]) . '">
                <i class="fa fa-download"></i> ' .
                $this->translator->trans('action.download') . '
            </a>';
        }

        if ($this->authorizationChecker->isGranted('ROLE_USER')) {
            if (\is_null($proof->getDeletedAt())) {
                $cellContent .= '<a href="' . $this->router->generate('registry_proof_edit', ['id' => $proof->getId()]) . '">
                    <i class="fa fa-pencil-alt"></i> ' .
                        $this->translator->trans('action.edit') . '
                </a>
                <a href="' . $this->router->generate('registry_proof_archive', ['id' => $proof->getId()]) . '">
                    <i class="fa fa-archive"></i> ' .
                    $this->translator->trans('action.archive') . '
                </a>';
            }
            $cellContent .= '<a href="' . $this->router->generate('registry_proof_delete', ['id' => $proof->getId()]) . '">
                <i class="fa fa-trash"></i> ' .
                $this->translator->trans('action.delete') . '
            </a>';
        }

        return $cellContent;
    }
}
