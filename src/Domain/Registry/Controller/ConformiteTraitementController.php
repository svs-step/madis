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
use App\Domain\AIPD\Converter\ModeleToAnalyseConverter;
use App\Domain\AIPD\Model\AnalyseImpact;
use App\Domain\AIPD\Repository as AipdRepository;
use App\Domain\Documentation\Model\Category;
use App\Domain\Registry\Form\Type\ConformiteTraitement\ConformiteTraitementType;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\Registry\Symfony\EventSubscriber\Event\ConformiteTraitementEvent;
use App\Domain\Reporting\Handler\WordHandler;
use App\Domain\User\Dictionary\UserRoleDictionary;
use App\Domain\User\Repository as UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
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

    private AipdRepository\ModeleAnalyse $modeleRepository;

    private RouterInterface $router;

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
        EventDispatcherInterface $dispatcher,
        Pdf $pdf,
        AipdRepository\ModeleAnalyse $modeleRepository,
        RouterInterface $router
    ) {
        parent::__construct($entityManager, $translator, $repository, $pdf, $userProvider, $authorizationChecker);
        $this->collectivityRepository = $collectivityRepository;
        $this->wordHandler            = $wordHandler;
        $this->authorizationChecker   = $authorizationChecker;
        $this->userProvider           = $userProvider;
        $this->treatmentRepository    = $treatmentRepository;
        $this->questionRepository     = $questionRepository;
        $this->dispatcher             = $dispatcher;
        $this->modeleRepository       = $modeleRepository;
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
        $user         = $this->userProvider->getAuthenticatedUser();

        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $collectivity = $user->getCollectivity();
        }

        if (\in_array(UserRoleDictionary::ROLE_REFERENT, $user->getRoles())) {
            $collectivity = \iterable_to_array($user->getCollectivitesReferees());
        }

        return $this->treatmentRepository->findAllActiveByCollectivityWithHasModuleConformiteTraitement($collectivity);
    }

    /**
     * {@inheritdoc}
     */
    public function listAction(): Response
    {
        $category = $this->entityManager->getRepository(Category::class)->findOneBy([
            'name' => 'Conformité des traitements',
        ]);

        $user          = $this->userProvider->getAuthenticatedUser();
        $services_user = $user->getServices();

        return $this->render($this->getTemplatingBasePath('list'), [
            'objects'       => $this->getListData(),
            'category'      => $category,
            'services_user' => $services_user,
        ]);
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
        $object = new Model\ConformiteTraitement\ConformiteTraitement();

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

        $serviceEnabled = $object->getTraitement()->getCollectivity()->getIsServicesEnabled();

        return $this->render($this->getTemplatingBasePath('create'), [
            'form'           => $form->createView(),
            'serviceEnabled' => $serviceEnabled,
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

        $serviceEnabled = $object->getTraitement()->getCollectivity()->getIsServicesEnabled();

        return $this->render($this->getTemplatingBasePath('edit'), [
            'form'           => $form->createView(),
            'serviceEnabled' => $serviceEnabled,
        ]);
    }

    public function startAipdAction(Request $request, string $id)
    {
        /** @var Model\ConformiteTraitement\ConformiteTraitement $conformiteTraitement */
        $conformiteTraitement = $this->repository->findOneById($id);
        if (!$conformiteTraitement) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        if ($request->isMethod('GET')) {
            return $this->render($this->getTemplatingBasePath('start'), [
                'totalItem'            => $this->modeleRepository->count(),
                'route'                => $this->router->generate('aipd_analyse_impact_modele_datatables', ['collectivity' => $conformiteTraitement->getTraitement()->getCollectivity()->getId()->toString()]),
                'conformiteTraitement' => $conformiteTraitement,
            ]);
        }

        if (!$request->request->has('modele_choice')) {
            throw new BadRequestHttpException('Parameter modele_choice must be present');
        }

        if (null === $modele = $this->modeleRepository->findOneById($request->request->get('modele_choice'))) {
            throw new NotFoundHttpException('No modele with Id ' . $request->request->get('modele_choice') . ' exists.');
        }

        $analyseImpact = ModeleToAnalyseConverter::createFromModeleAnalyse($modele);
        $analyseImpact->setConformiteTraitement($conformiteTraitement);
        $this->setAnalyseReponsesQuestionConformite($analyseImpact, $conformiteTraitement);
        $this->entityManager->persist($analyseImpact);

        foreach ($analyseImpact->getScenarioMenaces() as $scenarioMenace) {
            if (null !== $scenarioMenace->getMesuresProtections()) {
                foreach ($scenarioMenace->getMesuresProtections() as $mesureProtection) {
                    $this->entityManager->persist($mesureProtection);
                }
            }
        }
        $this->entityManager->flush();

        return $this->redirectToRoute('aipd_analyse_impact_create', [
            'id' => $analyseImpact->getId(),
        ]);
    }

    private function setAnalyseReponsesQuestionConformite(AnalyseImpact &$analyseImpact, Model\ConformiteTraitement\ConformiteTraitement $conformiteTraitement)
    {
        foreach ($conformiteTraitement->getReponses() as $reponse) {
            $analyseImpact->getQuestionConformitesOfPosition($reponse->getQuestion()->getPosition())->setReponseConformite($reponse);
        }
    }
}
