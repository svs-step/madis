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
use App\Domain\Registry\Dictionary\RequestObjectDictionary;
use App\Domain\Registry\Dictionary\RequestStateDictionary;
use App\Domain\Registry\Form\Type\RequestType;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\Reporting\Handler\WordHandler;
use App\Domain\User\Dictionary\UserRoleDictionary;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property Repository\Request $repository
 */
class RequestController extends CRUDController
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
     * @var RouterInterface
     */
    protected $router;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\Request $repository,
        RequestStack $requestStack,
        WordHandler $wordHandler,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProvider $userProvider,
        Pdf $pdf,
        RouterInterface $router
    ) {
        parent::__construct($entityManager, $translator, $repository, $pdf, $userProvider, $authorizationChecker);
        $this->requestStack         = $requestStack;
        $this->wordHandler          = $wordHandler;
        $this->authorizationChecker = $authorizationChecker;
        $this->userProvider         = $userProvider;
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
        return 'request';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\Request::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormType(): string
    {
        return RequestType::class;
    }

    /**
     * {@inheritdoc}
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
     */
    protected function isSoftDelete(): bool
    {
        return true;
    }

    /**
     * Generate a word report of contractors.
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public function reportAction(): Response
    {
        $objects = $this->repository->findAllByCollectivity(
            $this->userProvider->getAuthenticatedUser()->getCollectivity(),
            false,
            ['date' => 'desc']
        );

        return $this->wordHandler->generateRegistryRequestReport($objects);
    }

    public function listAction(): Response
    {
        $criteria = $this->getRequestCriteria();

        $category = $this->entityManager->getRepository(Category::class)->findOneBy([
            'name' => 'Demande',
        ]);

        return $this->render($this->getTemplatingBasePath('list'), [
            'totalItem' => $this->repository->count($criteria),
            'category'  => $category,
            'route'     => $this->router->generate('registry_request_list_datatables', ['archive' => $criteria['archive']]),
        ]);
    }

    public function listDataTables(Request $request): JsonResponse
    {
        $criteria = $this->getRequestCriteria();
        $demandes = $this->getResults($request, $criteria);

        $reponse = $this->getBaseDataTablesResponse($request, $demandes, $criteria);

        $yes = '<span class="label label-success">' . $this->translator->trans('label.yes') . '</span>';
        $no  = '<span class="label label-danger">' . $this->translator->trans('label.no') . '</span>';
        // die();
        /** @var Model\Request $demande */
        foreach ($demandes as $demande) {
            $reponse['data'][] = [
                'collectivite'       => $demande->getCollectivity()->getName(),
                'personne_concernee' => $this->getLinkForPersonneConcernee($demande),
                'date_demande'       => null !== $demande->getDate() ? \date_format($demande->getDate(), 'd/m/Y') : '',
                'objet_demande'      => $demande->getObject() ? RequestObjectDictionary::getObjects()[$demande->getObject()] : '',
                'demande_complete'   => $demande->isComplete() ? $yes : $no,
                'demandeur_legitime' => $demande->isLegitimateApplicant() ? $yes : $no,
                'demande_legitime'   => $demande->isLegitimateRequest() ? $yes : $no,
                'date_traitement'    => null !== $demande->getAnswer()->getDate() ? \date_format($demande->getAnswer()->getDate(), 'd/m/Y') : '',
                'etat_demande'       => $demande->getState() ? RequestStateDictionary::getStates()[$demande->getState()] : '',
                'actions'            => $this->getActionsCellContent($demande),
            ];
        }

        $jsonResponse = new JsonResponse();
        $jsonResponse->setJson(\json_encode($reponse));

        return $jsonResponse;
    }

    private function isRequestInUserServices(Model\Request $request): bool
    {
        $user   = $this->userProvider->getAuthenticatedUser();

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return $request->isInUserServices($user);
    }

    protected function getLabelAndKeysArray(): array
    {
        return [
            0 => 'collectivite',
            1 => 'personne_concernee',
            2 => 'date_demande',
            3 => 'objet_demande',
            4 => 'demande_complete',
            5 => 'demandeur_legitime',
            6 => 'demande_legitime',
            7 => 'date_traitement',
            8 => 'etat_demande',
            9 => 'actions',
        ];
    }

    private function getRequestCriteria()
    {
        $criteria            = [];
        $criteria['archive'] = $this->requestStack->getMasterRequest()->query->getBoolean('archive');
        $user                = $this->userProvider->getAuthenticatedUser();

        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $criteria['collectivity'] = $user->getCollectivity();
        }

        if (\in_array(UserRoleDictionary::ROLE_REFERENT, $user->getRoles())) {
            $criteria['collectivity'] = $user->getCollectivitesReferees();
        }

        return $criteria;
    }

    private function getActionsCellContent(Model\Request $demande)
    {
        $user = $this->userProvider->getAuthenticatedUser();
        if ($user->getServices()->isEmpty() || $this->isRequestInUserServices($demande)) {
            return
                '<a href="' . $this->router->generate('registry_request_edit', ['id' => $demande->getId()]) . '">
                    <i class="fa fa-pencil-alt"></i>' .
                    $this->translator->trans('action.edit') . '
                </a>
                <a href="' . $this->router->generate('registry_request_delete', ['id'=> $demande->getId()]) . '">
                    <i class="fa fa-trash"></i>' .
                    $this->translator->trans('action.archive') .
                '</a>';
        }

        return null;
    }

    private function getLinkForPersonneConcernee(Model\Request $demande)
    {
        $link = '<a href="' . $this->router->generate('registry_request_show', ['id'=> $demande->getId()]) . '">';
        if ($demande->getApplicant()->isConcernedPeople() ||
            ' ' === $demande->getConcernedPeople()->getFullName()) {
            $link .= $demande->getApplicant()->getFullName();
        } else {
            $link .= $demande->getConcernedPeople()->getFullName();
        }

        return $link . '</a>';
    }
}
