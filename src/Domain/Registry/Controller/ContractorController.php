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
use App\Domain\Registry\Form\Type\ContractorType;
use App\Domain\Registry\Model;
use App\Domain\Registry\Model\Contractor;
use App\Domain\Registry\Repository;
use App\Domain\Reporting\Handler\WordHandler;
use App\Domain\User\Dictionary\UserRoleDictionary;
use App\Domain\User\Model as UserModel;
use App\Domain\User\Repository as UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Polyfill\Intl\Icu\Exception\MethodNotImplementedException;

/**
 * @property Repository\Contractor $repository
 */
class ContractorController extends CRUDController
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
     * @var RouterInterface
     */
    protected $router;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\Contractor $repository,
        UserRepository\Collectivity $collectivityRepository,
        WordHandler $wordHandler,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProvider $userProvider,
        Pdf $pdf,
        RouterInterface $router
    ) {
        parent::__construct($entityManager, $translator, $repository, $pdf, $userProvider, $authorizationChecker);
        $this->collectivityRepository = $collectivityRepository;
        $this->wordHandler            = $wordHandler;
        $this->authorizationChecker   = $authorizationChecker;
        $this->userProvider           = $userProvider;
        $this->router                 = $router;
    }

    protected function getDomain(): string
    {
        return 'registry';
    }

    protected function getModel(): string
    {
        return 'contractor';
    }

    protected function getModelClass(): string
    {
        return Model\Contractor::class;
    }

    protected function getFormType(): string
    {
        return ContractorType::class;
    }

    public function listAction(): Response
    {
        $category = $this->entityManager->getRepository(Category::class)->findOneBy([
            'name' => 'Sous-traitant',
        ]);

        return $this->render($this->getTemplatingBasePath('list'), [
            'totalItem' => $this->repository->count($this->getRequestCriteria()),
            'category'  => $category,
            'route'     => $this->router->generate('registry_contractor_list_datatables'),
        ]);
    }

    protected function getListData()
    {
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->repository->findAll();
        }

        return $this->repository->findAllByCollectivity($this->userProvider->getAuthenticatedUser()->getCollectivity());
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
            ['name' => 'asc']
        );

        return $this->wordHandler->generateRegistryContractorReport($objects);
    }

    /**
     * Get all active treatments of a collectivity and return their id/name as JSON.
     */
    public function apiGetContractorsByCollectivity(string $collectivityId): Response
    {
        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException('You can\'t access to a collectivity contractor data');
        }

        /** @var UserModel\Collectivity|null $collectivity */
        $collectivity = $this->collectivityRepository->findOneById($collectivityId);
        if (null === $collectivity) {
            throw new NotFoundHttpException('Can\'t find collectivity for id ' . $collectivityId);
        }

        $contractors = $this->repository->findAllByCollectivity(
            $collectivity,
            [
                'name' => 'ASC',
            ]
        );
        $responseData = [];

        /** @var Model\Contractor $contractor */
        foreach ($contractors as $contractor) {
            $responseData[] = [
                'value' => $contractor->getId()->toString(),
                'text'  => $contractor->__toString(),
            ];
        }

        return new JsonResponse($responseData);
    }

    public function listDataTables(Request $request): JsonResponse
    {
        $criteria    = $this->getRequestCriteria();
        $contractors = $this->getResults($request, $criteria);
        $reponse     = $this->getBaseDataTablesResponse($request, $contractors, $criteria);

        $yes = '<span class="badge bg-green">' . $this->translator->trans('global.label.yes') . '</span>';
        $no  = '<span class="badge bg-red">' . $this->translator->trans('global.label.no') . '</span>';

        /** @var Model\Contractor $contractor */
        foreach ($contractors as $contractor) {
            $contractorLink = '<a href="' . $this->router->generate('registry_contractor_show', ['id' => $contractor->getId()->toString()]) . '">
                ' . \htmlspecialchars($contractor->getName()) . '
            </a>';

            $reponse['data'][] = [
                'id'                     => $contractor->getId(),
                'nom'                    => $contractorLink,
                'collectivite'           => $contractor->getCollectivity()->getName(),
                'clauses_contractuelles' => $contractor->isContractualClausesVerified() ? $yes : $no,
                'element_securite'       => $contractor->isAdoptedSecurityFeatures() ? $yes : $no,
                'registre_traitements'   => $contractor->isMaintainsTreatmentRegister() ? $yes : $no,
                'donnees_hors_eu'        => $contractor->isSendingDataOutsideEu() ?
                    '<span class="badge bg-red">' . $this->translator->trans('global.label.yes') . '</span>' :
                    '<span class="badge bg-green">' . $this->translator->trans('global.label.no') . '</span>',
                'createdAt' => date_format($contractor->getCreatedAt(), 'd-m-Y H:i'),
                'updatedAt' => date_format($contractor->getUpdatedAt(), 'd-m-Y H:i'),
                'actions'   => $this->getActionCellsContent($contractor),
            ];
        }

        $jsonResponse = new JsonResponse();
        $jsonResponse->setJson(\json_encode($reponse));

        return $jsonResponse;
    }

    private function isContractorInUserServices(Model\Contractor $contractor): bool
    {
        $user = $this->userProvider->getAuthenticatedUser();

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return $contractor->isInUserServices($user);
    }

    protected function getLabelAndKeysArray(): array
    {
        return [
            0 => 'nom',
            1 => 'collectivite',
            2 => 'clauses_contractuelles',
            3 => 'element_securite',
            4 => 'registre_traitements',
            5 => 'donnees_hors_eu',
            6 => 'createdAt',
            7 => 'updatedAt',
            8 => 'actions',
        ];
    }

    private function getRequestCriteria()
    {
        $criteria = [];
        $user     = $this->userProvider->getAuthenticatedUser();

        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $criteria['collectivity'] = $user->getCollectivity();
        }

        if (\in_array(UserRoleDictionary::ROLE_REFERENT, $user->getRoles())) {
            $criteria['collectivity'] = $user->getCollectivitesReferees();
        }

        return $criteria;
    }

    private function getActionCellsContent(Contractor $sousTraitant)
    {
        $user = $this->userProvider->getAuthenticatedUser();
        if ($user->getServices()->isEmpty() || $this->isContractorInUserServices($sousTraitant)) {
            $cellContent = '<a href="' . $this->router->generate('registry_contractor_edit', ['id' => $sousTraitant->getId()]) . '">
                    <i aria-hidden="true" class="fa fa-pencil"></i> ' .
                    $this->translator->trans('global.action.edit') .
                '</a>';

            $cellContent .=
                '<a href="' . $this->router->generate('registry_contractor_delete', ['id' => $sousTraitant->getId()]) . '">
                    <i aria-hidden="true" class="fa fa-trash"></i> ' .
                    $this->translator->trans('global.action.delete') .
                '</a>';

            return $cellContent;
        }

        return null;
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
            /* Delete clonedFrom id from clone to prevent SQL error on foreign key */
            foreach ($this->repository->findBy(['clonedFrom' => $id]) as $clone) {
                $clone->setClonedFrom(null);
            }
            $this->entityManager->remove($object);
            $this->entityManager->flush();
        }

        $this->addFlash('success', $this->getFlashbagMessage('success', 'delete', $object));

        return $this->redirectToRoute($this->getRouteName('list'));
    }
}
