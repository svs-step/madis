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
use App\Domain\Registry\Form\Type\ConformiteTraitement\ConformiteTraitementType;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\Registry\Symfony\EventSubscriber\Event\ConformiteTraitementEvent;
use App\Domain\Reporting\Handler\WordHandler;
use App\Domain\User\Repository as UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property Repository\ConformiteTraitement\ConformiteTraitement $repository
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

    /**
     * @var Repository\ConformiteTraitement\Question
     */
    protected $questionRepository;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\ConformiteTraitement\ConformiteTraitement $repository,
        UserRepository\Collectivity $collectivityRepository,
        WordHandler $wordHandler,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProvider $userProvider,
        Repository\Treatment $treatmentRepository,
        Repository\ConformiteTraitement\Question $questionRepository,
        EventDispatcherInterface $dispatcher
    ) {
        parent::__construct($entityManager, $translator, $repository);
        $this->collectivityRepository = $collectivityRepository;
        $this->wordHandler            = $wordHandler;
        $this->authorizationChecker   = $authorizationChecker;
        $this->userProvider           = $userProvider;
        $this->treatmentRepository    = $treatmentRepository;
        $this->questionRepository     = $questionRepository;
        $this->dispatcher             = $dispatcher;
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
        return 'conformite_traitement';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\ConformiteTraitement\ConformiteTraitement::class;
    }

    public function reportAction()
    {
        $objects = $this->treatmentRepository->findAllByCollectivity(
            $this->userProvider->getAuthenticatedUser()->getCollectivity()
        );

        return $this->wordHandler->generateRegistryConformiteTraitementReport($objects);
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormType(): string
    {
        return ConformiteTraitementType::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getListData()
    {
        $collectivity = null;

        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $collectivity = $this->userProvider->getAuthenticatedUser()->getCollectivity();
        }

        return $this->treatmentRepository->findAllActiveByCollectivityWithHasModuleConformiteTraitement($collectivity);
    }

    /**
     * {@inheritdoc}
     * Override method in order to hydrate questions.
     */
    public function createAction(Request $request): Response
    {
        /**
         * @var Model\ConformiteTraitement\ConformiteTraitement
         */
        $object     = new Model\ConformiteTraitement\ConformiteTraitement();

        $traitement = $this->treatmentRepository->findOneById($request->get('idTraitement'));
        $object->setTraitement($traitement);

        // Before create form, hydrate answers array with potential question responses
        foreach ($this->questionRepository->findAll(['position' => 'ASC']) as $question) {
            $reponse = new Model\ConformiteTraitement\Reponse();
            $reponse->setQuestion($question);
            $object->addReponse($reponse);
        }

        $form = $this->createForm($this->getFormType(), $object);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($object);
            $em->flush();

            $this->addFlash('success', $this->getFlashbagMessage('success', 'create', $object));

            return $this->redirectToRoute($this->getRouteName('list'));
        }

        return $this->render($this->getTemplatingBasePath('create'), [
            'form' => $form->createView(),
        ]);
    }

    /**
     * {@inheritdoc}
     * Override method in order to hydrate new questions.
     *
     * @param string $id The ID of the data to edit
     */
    public function editAction(Request $request, string $id): Response
    {
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        // Before create form, hydrate new answers array with potential question responses
        foreach ($this->questionRepository->findNewQuestionsNotUseInGivenConformite($object) as $question) {
            $reponse = new Model\ConformiteTraitement\Reponse();
            $reponse->setQuestion($question);
            $object->addReponse($reponse);
        }

        $form = $this->createForm($this->getFormType(), $object, ['validation_groups' => ['default', $this->getModel(), 'edit']]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->formPrePersistData($object);
            $this->entityManager->persist($object);
            $this->entityManager->flush();

            $this->dispatcher->dispatch(new ConformiteTraitementEvent($object));

            $this->addFlash('success', $this->getFlashbagMessage('success', 'edit', $object));

            return $this->redirectToRoute($this->getRouteName('list'));
        }

        return $this->render($this->getTemplatingBasePath('edit'), [
            'form' => $form->createView(),
        ]);
    }
}
