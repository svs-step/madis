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
use App\Domain\Registry\Form\Type\TreatmentType;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\Reporting\Handler\WordHandler;
use App\Domain\User\Model as UserModel;
use App\Domain\User\Repository as UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property Repository\Treatment $repository
 */
class TreatmentController extends CRUDController
{
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

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\Treatment $repository,
        UserRepository\Collectivity $collectivityRepository,
        RequestStack $requestStack,
        WordHandler $wordHandler,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProvider $userProvider
    ) {
        parent::__construct($entityManager, $translator, $repository);
        $this->collectivityRepository = $collectivityRepository;
        $this->requestStack           = $requestStack;
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
    protected function getListData()
    {
        $request = $this->requestStack->getMasterRequest();
        $active  = 'true' === $request->query->get('active') || \is_null($request->query->get('active'))
            ? true
            : false
        ;

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->repository->findAllActive($active);
        }

        return $this->repository->findAllActiveByCollectivity(
            $this->userProvider->getAuthenticatedUser()->getCollectivity(),
            $active
        );
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
        $objects = $this->repository->findAllActiveByCollectivity(
            $this->userProvider->getAuthenticatedUser()->getCollectivity(),
            true,
            ['name' => 'asc']
        );

        return $this->wordHandler->generateRegistryTreatmentReport($objects);
    }

    /**
     * Get all active treatments of a collectivity and return their id/name as JSON.
     *
     * @param string $collectivityId
     *
     * @return Response
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
}
