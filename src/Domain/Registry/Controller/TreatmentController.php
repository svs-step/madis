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
use App\Domain\Registry\Dictionary\TreatmentAuthorDictionary;
use App\Domain\Registry\Dictionary\TreatmentLegalBasisDictionary;
use App\Domain\Registry\Form\Type\TreatmentType;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\Reporting\Handler\WordHandler;
use App\Domain\User\Dictionary\UserRoleDictionary;
use App\Domain\User\Model as UserModel;
use App\Domain\User\Repository as UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property Repository\Treatment $repository
 */
class TreatmentController extends CRUDController
{
    use ServersideDatatablesTrait;

    /**
     * @var UserRepository\Collectivity
     */
    protected $collectivityRepository;
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
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\Treatment $repository,
        UserRepository\Collectivity $collectivityRepository,
        RequestStack $requestStack,
        WordHandler $wordHandler,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProvider $userProvider,
        Pdf $pdf,
        RouterInterface $router
    ) {
        parent::__construct($entityManager, $translator, $repository, $pdf);
        $this->collectivityRepository = $collectivityRepository;
        $this->requestStack           = $requestStack;
        $this->wordHandler            = $wordHandler;
        $this->authorizationChecker   = $authorizationChecker;
        $this->userProvider           = $userProvider;
        $this->router                 = $router;
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
        return 'treatment';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\Treatment::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormType(): string
    {
        return TreatmentType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function listAction(): Response
    {
        $request            = $this->requestStack->getMasterRequest();
        $criteria['active'] = 'true' === $request->query->get('active') || \is_null($request->query->get('active'))
            ? true
            : false
        ;

        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $criteria['collectivity'] = $this->userProvider->getAuthenticatedUser()->getCollectivity();
        }

        return $this->render('Registry/Treatment/list.html.twig', [
            'totalItem' => $this->repository->count($criteria),
            'route'     => $this->router->generate('registry_treatment_list_datatables', ['active' => $criteria['active']]),
        ]);
    }

    /**
     * Generate a word report of contractors.
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public function reportAction(): Response
    {
        $objects = $this->repository->findAllActiveByCollectivity(
            $this->userProvider->getAuthenticatedUser()->getCollectivity(),
            true,
            ['name' => 'asc']
        );

        return $this->wordHandler->generateRegistryTreatmentReport($objects);
    }

    /**
     * Get all active treatments of a collectivity and return their id/name as JSON.
     */
    public function apiGetTreatmentsByCollectivity(string $collectivityId): Response
    {
        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException('You can\'t access to a collectivity treatment data');
        }

        /** @var UserModel\Collectivity|null $collectivity */
        $collectivity = $this->collectivityRepository->findOneById($collectivityId);
        if (null === $collectivity) {
            throw new NotFoundHttpException('Can\'t find collectivity for id ' . $collectivityId);
        }

        $treatments = $this->repository->findAllByCollectivity(
            $collectivity,
            [
                'active' => 'DESC',
                'name'   => 'ASC',
            ]
        );
        $responseData = [];

        /** @var Model\Treatment $treatment */
        foreach ($treatments as $treatment) {
            $responseData[] = [
                'value' => $treatment->getId()->toString(),
                'text'  => $treatment->isActive() ? $treatment->__toString() : '(Inactif) ' . $treatment->__toString(),
            ];
        }

        return new JsonResponse($responseData);
    }

    /**
     * {@inheritdoc}
     */
    public function listDataTables(Request $request): JsonResponse
    {
        $request            = $this->requestStack->getMasterRequest();
        $criteria['active'] = $request->query->getBoolean('active');
        $user               = $this->userProvider->getAuthenticatedUser();

        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $criteria['collectivity'] = $user->getCollectivity();
        }

        if (\in_array(UserRoleDictionary::ROLE_REFERENT, $user->getRoles())) {
            $criteria['collectivity'] = $user->getCollectivitesReferees();
        }

        /** @var Paginator $treatments */
        $treatments  = $this->getResults($request, $criteria);

        $reponse = $this->getBaseDataTablesResponse($request, $treatments, $criteria);

        /** @var Model\Treatment $treatment */
        foreach ($treatments as $treatment) {
            $treatmentLink = '<a href="' . $this->router->generate('registry_treatment_show', ['id' => $treatment->getId()->toString()]) . '">
                ' . $treatment->getName() . '
            </a>';

            $contractors = '<ul>';
            foreach ($treatment->getContractors() as $contractor) {
                $contractors .= '<li>' . $contractor->getName() . '</li>';
            }
            $contractors .= '</ul>';

            $yes = '<span class="badge bg-green">' . $this->translator->trans('label.yes') . '</span>';
            $no  = '<span class="badge bg-orange">' . $this->translator->trans('label.no') . '</span>';

            $reponse['data'][] = [
                'nom'                    => $treatmentLink,
                'collectivite'           => $treatment->getCollectivity()->getName(),
                'baseLegal'              => !empty($treatment->getLegalBasis()) ? TreatmentLegalBasisDictionary::getBasis()[$treatment->getLegalBasis()] : null,
                'logiciel'               => $treatment->getSoftware(),
                'enTantQue'              => !empty($treatment->getAuthor()) ? TreatmentAuthorDictionary::getAuthors()[$treatment->getAuthor()] : null,
                'gestionnaire'           => $treatment->getManager(),
                'sousTraitant'           => $contractors,
                'controleAcces'          => $treatment->getSecurityAccessControl()->isCheck() ? $yes : $no,
                'tracabilite'            => $treatment->getSecurityTracability()->isCheck() ? $yes : $no,
                'saving'                 => $treatment->getSecuritySaving()->isCheck() ? $yes : $no,
                'update'                 => $treatment->getSecurityUpdate()->isCheck() ? $yes : $no,
                'other'                  => $treatment->getSecurityOther()->isCheck() ? $yes : $no,
                'entitledPersons'        => $treatment->isSecurityEntitledPersons() ? $yes : $no,
                'openAccounts'           => $treatment->isSecurityOpenAccounts() ? $yes : $no,
                'specificitiesDelivered' => $treatment->isSecuritySpecificitiesDelivered() ? $yes : $no,
                'updatedAt'              => date_format($treatment->getUpdatedAt(), 'd-m-Y H:i:s'),
                'actions'                => $this->generateActionCellContent($treatment),
            ];
        }

        $jsonResponse = new JsonResponse();
        $jsonResponse->setJson(json_encode($reponse));

        return $jsonResponse;
    }

    private function generateActionCellContent(Model\Treatment $treatment)
    {
        $id         = $treatment->getId();
        $editPath   = $this->router->generate('registry_treatment_edit', ['id' => $id]);
        $deletePath = $this->router->generate('registry_treatment_delete', ['id' => $id]);

        return '<a href="' . $editPath . '">
            <i class="fa fa-pencil-alt"></i>
                ' . $this->translator->trans('action.edit') . '
            </a>
            <a href="' . $deletePath . '">
                <i class="fa fa-trash"></i>
                ' . $this->translator->trans('action.delete') . '
            </a>'
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getLabelAndKeysArray(): array
    {
        return [
            '0'  => 'name',
            '1'  => 'collectivite',
            '2'  => 'baseLegal',
            '3'  => 'logiciel',
            '4'  => 'enTantQue',
            '5'  => 'gestionnaire',
            '6'  => 'sousTraitant',
            '7'  => 'controleAcces',
            '8'  => 'tracabilite',
            '9'  => 'saving',
            '10' => 'update',
            '11' => 'other',
            '12' => 'entitledPersons',
            '13' => 'openAccounts',
            '14' => 'specificitiesDelivered',
            '15' => 'updatedAt',
            '16' => 'actions',
        ];
    }
}
