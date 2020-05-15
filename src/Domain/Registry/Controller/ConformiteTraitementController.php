<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author ANODE <contact@agence-anode.fr>
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
use App\Domain\Registry\Form\Type\MesurementType;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\Reporting\Handler\WordHandler;
use App\Domain\User\Repository as UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property Repository\ConformiteTraitement $repository
 */
class ConformiteTraitementController extends CRUDController
{
    /**
     * @var UserRepository\Collectivity
     */
    protected $collectivityRepository;

    /**
     * @var Repository\Treatment
     */
    protected $treatmentRepository;

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
        Repository\ConformiteTraitement $repository,
        UserRepository\Collectivity $collectivityRepository,
        WordHandler $wordHandler,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProvider $userProvider,
        Repository\Treatment $treatmentRepository
    ) {
        parent::__construct($entityManager, $translator, $repository);
        $this->collectivityRepository = $collectivityRepository;
        $this->wordHandler            = $wordHandler;
        $this->authorizationChecker   = $authorizationChecker;
        $this->userProvider           = $userProvider;
        $this->treatmentRepository    = $treatmentRepository;
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
        return 'conformiteTraitement';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\ConformiteTraitement\ConformiteTraitement::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormType(): string
    {
        //TODO
        return 'foo';
//        return MesurementType::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getListData()
    {
        $collectivity = $this->userProvider->getAuthenticatedUser()->getCollectivity();

        return $this->treatmentRepository->findAllActiveByCollectivity($collectivity);
    }

    /**
     * The show action view
     * Display the object information.
     *
     * @param string $id The ID of the data to display
     */
    public function showAction(string $id): Response
    {
        return new Response();
        //TODO
//        $object = $this->repository->findOneById($id);
//        if (!$object) {
//            throw new NotFoundHttpException("No object found with ID '{$id}'");
//        }
//
//        return $this->render($this->getTemplatingBasePath('show'), [
//            'object' => $object,
//        ]);
    }
}
