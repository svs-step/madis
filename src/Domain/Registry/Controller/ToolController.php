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
use App\Domain\Registry\Dictionary\MesurementStatusDictionary;
use App\Domain\Registry\Dictionary\ToolTypeDictionary;
use App\Domain\Registry\Form\Type\ToolType;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\Reporting\Handler\WordHandler;
use App\Domain\User\Dictionary\UserRoleDictionary;
use App\Domain\User\Repository as UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Polyfill\Intl\Icu\Exception\MethodNotImplementedException;

/**
 * @property Repository\Mesurement $repository
 */
class ToolController extends CRUDController
{
    use ServersideDatatablesTrait;

    /**
     * @var UserRepository\Collectivity
     */
    protected $collectivityRepository;

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
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\Tool $repository,
        UserRepository\Collectivity $collectivityRepository,
        WordHandler $wordHandler,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProvider $userProvider,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        Pdf $pdf,
        RequestStack $requestStack
    ) {
        parent::__construct($entityManager, $translator, $repository, $pdf, $userProvider, $authorizationChecker);
        $this->collectivityRepository = $collectivityRepository;
        $this->wordHandler            = $wordHandler;
        $this->authorizationChecker   = $authorizationChecker;
        $this->userProvider           = $userProvider;
        $this->formFactory            = $formFactory;
        $this->router                 = $router;
        $this->requestStack           = $requestStack;
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
        return 'tool';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\Tool::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormType(): string
    {
        return ToolType::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getListData()
    {
        $request  = $this->requestStack->getCurrentRequest();
        $criteria = $this->getRequestCriteria($request);

        return $this->repository->findBy($criteria);
    }

    public function listAction(): Response
    {
        $request  = $this->requestStack->getCurrentRequest();
        $category = $this->entityManager->getRepository(Category::class)->findOneBy([
            'name' => 'Logiciels',
        ]);

        return $this->render($this->getTemplatingBasePath('list'), [
            'totalItem' => $this->repository->count($this->getRequestCriteria($request)),
            'category'  => $category,
            'route'     => $this->router->generate('registry_tool_list_datatables'),
        ]);
    }

    private function getRequestCriteria(Request $request)
    {
        $criteria = [];
        $user     = $this->userProvider->getAuthenticatedUser();

        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $criteria['collectivity'] = $user->getCollectivity();
        }

        if (\in_array(UserRoleDictionary::ROLE_REFERENT, $user->getRoles())) {
            $criteria['collectivity'] = $user->getCollectivitesReferees();
        }

        if ($request->query->getBoolean('action_plan')) {
            // Since we have to display planified & not-applied mesurement, filter
            $criteria['planificationDate'] = 'null';
            $criteria['status']            = MesurementStatusDictionary::STATUS_NOT_APPLIED;
        }

        return $criteria;
    }

    protected function getLabelAndKeysArray(): array
    {
        return [
            'name',
            'collectivity',
            'type',
            'editor',
            'archival',
            'encrypted',
            'access_control',
            'update',
            'backup',
            'deletion',
            'tracking',
            'has_comment',
            'other',
            'treatments',
            'contractors',
            'documents',
            'mesurements',
            'createdAt',
            'updatedAt',
            'actions',
        ];
    }

    public function listDataTables(Request $request): JsonResponse
    {
        $criteria = $this->getRequestCriteria($request);
        $tools    = $this->getResults($request, $criteria);
        $reponse  = $this->getBaseDataTablesResponse($request, $tools, $criteria);

        $yes = '<span class="badge bg-green">' . $this->translator->trans('label.yes') . '</span>';
        $no  = '<span class="badge bg-orange">' . $this->translator->trans('label.no') . '</span>';

        /** @var Model\Tool $tool */
        foreach ($tools as $tool) {
            $reponse['data'][] = [
                'id'             => $tool->getId(),
                'name'           => $this->generateShowLink($tool),
                'collectivity'   => $tool->getCollectivity()->getName(),
                'type'           => ToolTypeDictionary::getTypes()[$tool->getType()],
                'editor'         => $tool->getEditor(),
                'archival'       => $tool->getArchival()->isCheck() ? $yes : $no,
                'encrypted'      => $tool->getEncrypted()->isCheck() ? $yes : $no,
                'access_control' => $tool->getAccessControl()->isCheck() ? $yes : $no,
                'update'         => $tool->getUpdate()->isCheck() ? $yes : $no,
                'backup'         => $tool->getBackup()->isCheck() ? $yes : $no,
                'deletion'       => $tool->getDeletion()->isCheck() ? $yes : $no,
                'tracking'       => $tool->getTracking()->isCheck() ? $yes : $no,
                'has_comment'    => $tool->getHasComment()->isCheck() ? $yes : $no,
                'other'          => $tool->getOther()->isCheck() ? $yes : $no,
                'treatments'     => $this->generateLinkedDataColumn($tool->getTreatments()),
                'contractors'    => $this->generateLinkedDataColumn($tool->getContractors()),
                'documents'      => '',
                'mesurements'    => $this->generateLinkedDataColumn($tool->getMesurements()),
                'createdAt'      => $tool->getCreatedAt()->format('d-m-Y H:i:s'),
                'updatedAt'      => $tool->getUpdatedAt()->format('d-m-Y H:i:s'),
                'actions'        => $this->generateActionCell($tool),
            ];
        }

        $jsonResponse = new JsonResponse();
        $jsonResponse->setJson(json_encode($reponse));

        return $jsonResponse;
    }

    private function generateLinkedDataColumn(iterable|Collection|null $data)
    {
        if (is_null($data)) {
            return '';
        }
        if (is_object($data) && method_exists($data, 'toArray')) {
            $data = $data->toArray();
        }

        return join(', ', array_map(function ($object) {
            return $object->getName();
        }, (array) $data));
    }

    private function generateShowLink(Model\Tool $tool)
    {
        return '<a href="' .
            $this->router->generate('registry_tool_show', ['id' => $tool->getId()]) .
            '">' . \htmlspecialchars($tool->getName()) . '</a>';
    }

    private function generateActionCell(Model\Tool $tool)
    {
        return '<a href="' .
            $this->router->generate('registry_tool_edit', ['id' => $tool->getId()]) . '">
            <i class="fa fa-pencil-alt"></i> ' .
            $this->translator->trans('registry.tool.action.edit')
            . '</a>
            
            <a href="' .
            $this->router->generate('registry_tool_delete', ['id' => $tool->getId()]) .
            '"><i class="fa fa-trash"></i> ' .
            $this->translator->trans('registry.tool.action.delete')
            . '</a>';
    }

    /**
     * The deletion action
     * Delete the data.
     * OVERRIDE of the CRUDController to manage clone id.
     *
     * @throws \Exception
     */
    public function deleteConfirmationAction(string $id): Response
    {
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
            $this->entityManager->remove($object);
            $this->entityManager->flush();
        }

        $this->addFlash('success', $this->getFlashbagMessage('success', 'delete', $object));

        return $this->redirectToRoute($this->getRouteName('list'));
    }
}
