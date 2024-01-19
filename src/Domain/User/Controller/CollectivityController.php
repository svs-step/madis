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
use App\Application\Symfony\Security\UserProvider;
use App\Application\Traits\ServersideDatatablesTrait;
use App\Domain\Registry\Model\Mesurement;
use App\Domain\Registry\Model\Treatment;
use App\Domain\Registry\Repository as RegistryRepository;
use App\Domain\User\Dictionary\CollectivityTypeDictionary;
use App\Domain\User\Dictionary\UserRoleDictionary;
use App\Domain\User\Form\Type\CollectivityType;
use App\Domain\User\Model;
use App\Domain\User\Repository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property Repository\Collectivity $repository
 */
class CollectivityController extends CRUDController
{
    use ServersideDatatablesTrait;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var Security
     */
    protected $security;

    protected RegistryRepository\Treatment $treatmentRepository;

    protected Repository\User $userRepository;

    protected RegistryRepository\Proof $proofRepository;

    protected RegistryRepository\Contractor $contractorRepository;

    protected RegistryRepository\Mesurement $mesurementRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\Collectivity $repository,
        Pdf $pdf,
        RouterInterface $router,
        Security $security,
        RegistryRepository\Treatment $treatmentRepository,
        RegistryRepository\Contractor $contractorRepository,
        RegistryRepository\Proof $proofRepository,
        RegistryRepository\Mesurement $mesurementRepository,
        Repository\User $userRepository,
        UserProvider $userProvider,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        parent::__construct($entityManager, $translator, $repository, $pdf, $userProvider, $authorizationChecker);
        $this->router               = $router;
        $this->security             = $security;
        $this->treatmentRepository  = $treatmentRepository;
        $this->contractorRepository = $contractorRepository;
        $this->proofRepository      = $proofRepository;
        $this->userRepository       = $userRepository;
        $this->mesurementRepository = $mesurementRepository;
        $this->userProvider         = $userProvider;
        $this->authorizationChecker = $authorizationChecker;
    }

    protected function getDomain(): string
    {
        return 'user';
    }

    protected function getModel(): string
    {
        return 'collectivity';
    }

    protected function getModelClass(): string
    {
        return Model\Collectivity::class;
    }

    protected function getFormType(): string
    {
        return CollectivityType::class;
    }

    public function listAction(): Response
    {
        $criteria = $this->getRequestCriteria();

        return $this->render($this->getTemplatingBasePath('list'), [
            'totalItem' => $this->repository->count($criteria),
            'route'     => $this->router->generate('user_collectivity_list_datatables'),
        ]);
    }

    public function listDataTables(Request $request): JsonResponse
    {
        $criteria       = $this->getRequestCriteria();
        $collectivities = $this->getResults($request, $criteria);
        $reponse        = $this->getBaseDataTablesResponse($request, $collectivities, $criteria);

        $active   = '<span class="badge bg-green">' . $this->translator->trans('global.label.active') . '</span>';
        $inactive = '<span class="badge bg-red">' . $this->translator->trans('global.label.inactive') . '</span>';
        /** @var Model\Collectivity $collectivity */
        foreach ($collectivities as $collectivity) {
            $reponse['data'][] = [
                'nom'                          => '<a aria-label="' . $collectivity->getName() . '" href="' . $this->router->generate('user_collectivity_show', ['id' => $collectivity->getId()]) . '">' . $collectivity->getName() . '</a>',
                'nom_court'                    => $collectivity->getShortName(),
                'type'                         => !\is_null($collectivity->getType()) ? CollectivityTypeDictionary::getTypes()[$collectivity->getType()] ?? $collectivity->getType() : null,
                'informations_complementaires' => !\is_null($collectivity->getInformationsComplementaires()) ? nl2br($collectivity->getInformationsComplementaires()) : null,
                'siren'                        => $collectivity->getSiren(),
                'statut'                       => $collectivity->isActive() ? $active : $inactive,
                'population'                   => $collectivity->getPopulation(),
                'nbr_agents'                   => $collectivity->getNbrAgents(),
                'nbr_cnil'                     => $collectivity->getNbrCnil(),
                'tel_referent_rgpd'            => !\is_null($collectivity->getDpo()) ? ($collectivity->getDpo())->getPhoneNumber() : null,
                'createdAt'                    => !\is_null($collectivity->getCreatedAt()) ? $collectivity->getCreatedAt()->format('d-m-Y H:i') : null,
                'updatedAt'                    => !\is_null($collectivity->getUpdatedAt()) ? $collectivity->getUpdatedAt()->format('d-m-Y H:i') : null,
                'actions'                      => $this->getActionCellsContent($collectivity),
            ];
        }

        $jsonResponse = new JsonResponse();
        $jsonResponse->setJson(\json_encode($reponse));

        return $jsonResponse;
    }

    private function getActionCellsContent(Model\Collectivity $collectivity)
    {
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        $cellContent = '<a aria-label="' . $this->translator->trans('global.action.edit') . '" href="' . $this->router->generate('user_collectivity_edit', ['id' => $collectivity->getId()]) . '">
            <i aria-hidden="true" class="fa fa-pencil"></i> ' .
            $this->translator->trans('global.action.edit') .
        '</a>';

        $cellContent .= '<a aria-label="' . $this->translator->trans('global.action.delete') . '" href="' . $this->router->generate('user_collectivity_delete', ['id' => $collectivity->getId()]) . '">
            <i aria-hidden="true" class="fa fa-trash"></i> ' .
            $this->translator->trans('global.action.delete') .
        '</a>';

        return $cellContent;
    }

    protected function getLabelAndKeysArray(): array
    {
        return [
            'nom',
            'nom_court',
            'type',
            'informations_complementaires',
            'statut',
            'date_maj',
            'population',
            'nbr_agents',
            'nbr_cnil',
            'createdAt',
            'updatedAt',
            'actions',
        ];
    }

    private function getRequestCriteria()
    {
        $criteria = [];

        if (!$this->security->isGranted('ROLE_ADMIN')) {
            /** @var Model\User $user */
            $user                              = $this->security->getUser();
            $criteria['collectivitesReferees'] = $user->getCollectivitesReferees();
        }

        return $criteria;
    }

    public function showAction(string $id): Response
    {
        /** @var Model\User $user */
        $user = $this->security->getUser();
        if (\in_array(UserRoleDictionary::ROLE_REFERENT, $user->getRoles())) {
            $collectivities = \array_filter(\iterable_to_array($user->getCollectivitesReferees()), function (Model\Collectivity $collectivity) use ($id) {
                return $collectivity->getId()->toString() === $id;
            });

            if (empty($collectivities)) {
                throw $this->createAccessDeniedException();
            }
        }

        return parent::showAction($id);
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

        $stringObjects = [];

        $deletedTreaments = $this->treatmentRepository->findBy(['collectivity' => $object]);
        foreach ($deletedTreaments as $deletedTreament) {
            $stringObjects[] = 'Traitement - ' . $deletedTreament->getName();
            /**
             * @var Treatment
             */
            $aipds = $deletedTreament->getConformiteTraitement() ? $deletedTreament->getConformiteTraitement()->getAnalyseImpacts() : [];
            foreach ($aipds as $aipd) {
                $stringObjects[] = 'AIPD - ' . $aipd->__toString();
            }
        }

        $deletedContractors = $this->contractorRepository->findBy(['collectivity' => $object]);
        foreach ($deletedContractors as $deletedContractor) {
            $stringObjects[] = 'Sous-traitant - ' . $deletedContractor->getName();
        }

        $deletedProofs = $this->proofRepository->findBy(['collectivity' => $object]);
        foreach ($deletedProofs as $deletedProof) {
            $stringObjects[] = 'Preuve - ' . $deletedProof->getName();
        }

        $deletedUsers = $this->userRepository->findBy(['collectivity' => $object]);
        foreach ($deletedUsers as $deletedUser) {
            $stringObjects[] = 'Utilisateur - ' . $deletedUser->getFirstname() . ' ' . $deletedUser->getLastname();
        }

        $deletedMesurements = $this->mesurementRepository->findBy(['collectivity' => $object]);
        foreach ($deletedMesurements as $deletedMesurement) {
            /*
             * @var Mesurement
             */
            if ($deletedMesurement->getClonedFrom()) {
                $stringObjects[] = 'Action de protection - ' . $deletedMesurement->getClonedFrom()->getName();
            }
            $stringObjects[] = 'Action de protection - ' . $deletedMesurement->getName();
        }

        return $this->render($this->getTemplatingBasePath('delete'), [
            'object'             => $object,
            'deletedObjects'     => $stringObjects,
            'deletedTreatments'  => $deletedTreaments,
            'deletedContractors' => $deletedContractors,
        ]);
    }

    public function deleteConfirmationAction(string $id): Response
    {
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        return $this->render($this->getTemplatingBasePath('delete_processing'), [
            'object' => $object,
        ]);

        $this->addFlash('success', $this->helper->trans('user.organization.flashbag.success.delete'));

        return $this->redirectToRoute($this->getRouteName('list'));
    }

    public function clonedFromOnNullAction(string $id)
    {
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        $this->treatmentRepository->resetClonedFromCollectivity($object);
        $this->mesurementRepository->resetClonedFromCollectivity($object);
        $this->contractorRepository->resetClonedFromCollectivity($object);

        $this->entityManager->flush();

        return new JsonResponse();
    }

    public function deleteRelatedObjectsAction($id, string $objectType)
    {
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        switch ($objectType) {
            case 'treatments':
                foreach ($this->treatmentRepository->findAllByCollectivity($object) as $treatment) {
                    $this->entityManager->remove($treatment);
                }
                break;
            case 'mesurements':
                foreach ($this->mesurementRepository->findAllByCollectivity($object) as $mesurement) {
                    $this->entityManager->remove($mesurement);
                }
                break;
            case 'contractors':
                foreach ($this->contractorRepository->findAllByCollectivity($object) as $mesurement) {
                    $this->entityManager->remove($mesurement);
                }
                break;
            case 'users':
                foreach ($this->userRepository->findBy(['collectivity' => $object]) as $user) {
                    $this->entityManager->remove($user);
                }
                break;
            case 'proofs':
                foreach ($this->proofRepository->findBy(['collectivity' => $object]) as $proof) {
                    $this->entityManager->remove($proof);
                }
                break;
        }
        $this->entityManager->flush();

        return new JsonResponse();
    }

    public function deleteCollectivityAction(string $id)
    {
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        $this->entityManager->remove($object);
        $this->entityManager->flush();

        $this->addFlash('success', $this->helper->trans('user.organization.flashbag.success.delete'));

        return $this->redirectToRoute($this->getRouteName('list'));
    }
}
