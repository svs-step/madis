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
use App\Domain\Registry\Dictionary\MesurementStatusDictionary;
use App\Domain\Registry\Form\Type\MesurementType;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\Reporting\Handler\WordHandler;
use App\Domain\User\Model as UserModel;
use App\Domain\User\Repository as UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class MesurementController extends CRUDController
{
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

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\Mesurement $repository,
        UserRepository\Collectivity $collectivityRepository,
        WordHandler $wordHandler,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProvider $userProvider
    ) {
        parent::__construct($entityManager, $translator, $repository);
        $this->collectivityRepository = $collectivityRepository;
        $this->wordHandler            = $wordHandler;
        $this->authorizationChecker   = $authorizationChecker;
        $this->userProvider           = $userProvider;
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
        return 'mesurement';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\Mesurement::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormType(): string
    {
        return MesurementType::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getListData()
    {
        $criteria = [];

        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $criteria['collectivity'] = $this->userProvider->getAuthenticatedUser()->getCollectivity();
        }

        return $this->repository->findBy($criteria);
    }

    /**
     * Generate a word report of contractors.
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     *
     * @return Response
     */
    public function reportAction(): Response
    {
        $objects = $this->repository->findAllByCollectivity(
            $this->userProvider->getAuthenticatedUser()->getCollectivity(),
            ['name' => 'asc']
        );

        return $this->wordHandler->generateRegistryMesurementReport($objects);
    }

    /**
     * Display list of action plan
     * Action plan are mesurement planified which are not yet applied.
     *
     * @return Response
     */
    public function actionPlanAction()
    {
        $criteria = [
            'status' => MesurementStatusDictionary::STATUS_NOT_APPLIED,
        ];

        // Since we have to display planified & not-applied mesurement, filter
        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $criteria['collectivity'] = $this->userProvider->getAuthenticatedUser()->getCollectivity();
        }

        return $this->render('Registry/Mesurement/action_plan.html.twig', [
            'objects' => $this->repository->findByPlanified($criteria),
        ]);
    }

    /**
     * Get all active treatments of a collectivity and return their id/name as JSON.
     *
     * @param string $collectivityId
     *
     * @return Response
     */
    public function apiGetMesurementsByCollectivity(string $collectivityId): Response
    {
        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException('You can\'t access to a collectivity mesurement data');
        }

        /** @var UserModel\Collectivity|null $collectivity */
        $collectivity = $this->collectivityRepository->findOneById($collectivityId);
        if (null === $collectivity) {
            throw new NotFoundHttpException('Can\'t find collectivity for id ' . $collectivityId);
        }

        $mesurements   = $this->repository->findAllByCollectivity(
            $collectivity,
            [
                'name' => 'ASC',
            ]
        );
        $responseData = [];

        /** @var Model\Mesurement $mesurement */
        foreach ($mesurements as $mesurement) {
            $responseData[] = [
                'value' => $mesurement->getId()->toString(),
                'text'  => $mesurement->__toString(),
            ];
        }

        return new JsonResponse($responseData);
    }
}
