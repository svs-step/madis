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
use App\Domain\Registry\Repository\Contractor as ContractorRepository;
use App\Domain\Registry\Repository\Proof as ProofRepository;
use App\Domain\Registry\Repository\Treatment as TreatmentRepository;
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

    protected TreatmentRepository $treatmentRepository;

    protected Repository\User $userRepository;

    protected ProofRepository $proofRepository;

    protected ContractorRepository $contractorRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\Collectivity $repository,
        Pdf $pdf,
        RouterInterface $router,
        Security $security,
        TreatmentRepository $treatmentRepository,
        ContractorRepository $contractorRepository,
        ProofRepository $proofRepository,
        Repository\User $userRepository,
        UserProvider $userProvider,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        parent::__construct($entityManager, $translator, $repository, $pdf, $userProvider, $authorizationChecker);
        $this->router                   = $router;
        $this->security                 = $security;
        $this->treatmentRepository      = $treatmentRepository;
        $this->contractorRepository     = $contractorRepository;
        $this->proofRepository          = $proofRepository;
        $this->userRepository           = $userRepository;
        $this->userProvider             = $userProvider;
        $this->authorizationChecker     = $authorizationChecker;
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
        return 'collectivity';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\Collectivity::class;
    }

    /**
     * {@inheritdoc}
     */
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

        $active   = '<span class="badge bg-green">' . $this->translator->trans('label.active') . '</span>';
        $inactive = '<span class="badge bg-red">' . $this->translator->trans('label.inactive') . '</span>';
        /** @var Model\Collectivity $collectivity */
        foreach ($collectivities as $collectivity) {
            $reponse['data'][] = [
                'nom'                          => '<a href="' . $this->router->generate('user_collectivity_show', ['id' => $collectivity->getId()]) . '">' .
                    $collectivity->getName() .
                    '</a>',
                'nom_court'                    => $collectivity->getShortName(),
                'type'                         => !\is_null($collectivity->getType()) ? CollectivityTypeDictionary::getTypes()[$collectivity->getType()] : null,
                'informations_complementaires' => !\is_null($collectivity->getInformationsComplementaires()) ? nl2br($collectivity->getInformationsComplementaires()) : null,
                'siren'                        => $collectivity->getSiren(),
                'statut'                       => $collectivity->isActive() ? $active : $inactive,
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

        $cellContent = '<a href="' . $this->router->generate('user_collectivity_edit', ['id'=> $collectivity->getId()]) . '">
            <i class="fa fa-pencil-alt"></i> ' .
            $this->translator->trans('action.edit') .
        '</a>';

        $cellContent .= '<a href="' . $this->router->generate('user_collectivity_delete', ['id'=> $collectivity->getId()]) . '">
            <i class="fa fa-trash"></i> ' .
            $this->translator->trans('action.delete') .
        '</a>';

        return $cellContent;
    }

    protected function getLabelAndKeysArray(): array
    {
        return [
            0 => 'nom',
            1 => 'nom_court',
            2 => 'type',
            3 => 'siren',
            4 => 'statut',
            5 => 'actions',
        ];
    }

    private function getRequestCriteria()
    {
        $criteria            = [];

        if (!$this->security->isGranted('ROLE_ADMIN')) {
            /** @var Model\User $user */
            $user                              = $this->security->getUser();
            $criteria['collectivitesReferees'] = $user->getCollectivitesReferees();
        }

        return $criteria;
    }

    /**
     * {@inheritdoc}
     */
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
        }

        $deletedContractors = $this->contractorRepository->findBy(['collectivity' => $object]);
        foreach ($deletedContractors as $deletedContractor) {
            $stringObjects[] = 'Sous-traitent - ' . $deletedContractor->getName();
        }

        $deletedProofs =  $this->proofRepository->findBy(['collectivity' => $object]);
        foreach ($deletedProofs as $deletedProof) {
            $stringObjects[] = 'Preuve - ' . $deletedProof->getName();
        }

        $deletedUsers =  $this->userRepository->findBy(['collectivity' => $object]);
        foreach ($deletedUsers as $deletedUser) {
            $stringObjects[] = 'Utilisateur - ' . $deletedUser->getFirstname() . ' ' . $deletedUser->getLastname();
        }

        return $this->render($this->getTemplatingBasePath('delete'), [
            'object'            => $object,
            'deletedObjects'    => $stringObjects,
        ]);
    }
}
