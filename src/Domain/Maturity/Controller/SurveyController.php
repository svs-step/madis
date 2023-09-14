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

namespace App\Domain\Maturity\Controller;

use App\Application\Controller\CRUDController;
use App\Application\Interfaces\CollectivityRelated;
use App\Application\Symfony\Security\UserProvider;
use App\Application\Traits\ServersideDatatablesTrait;
use App\Domain\Documentation\Model\Category;
use App\Domain\Maturity\Calculator\MaturityHandler;
use App\Domain\Maturity\Form\Type\SurveyType;
use App\Domain\Maturity\Form\Type\SyntheseType;
use App\Domain\Maturity\Model;
use App\Domain\Maturity\Repository;
use App\Domain\Reporting\Handler\WordHandler;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property Repository\Survey $repository
 */
class SurveyController extends CRUDController
{
    use ServersideDatatablesTrait;

    /**
     * @var WordHandler
     */
    private $wordHandler;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var UserProvider
     */
    protected $userProvider;

    /**
     * @var MaturityHandler
     */
    protected $maturityHandler;

    protected Repository\Question $questionRepository;

    private Repository\Referentiel $referentielRepository;
    private $router;
    private RequestStack $requestStack;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\Survey $repository,
        Repository\Question $questionRepository,
        WordHandler $wordHandler,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProvider $userProvider,
        MaturityHandler $maturityHandler,
        Pdf $pdf,
        Repository\Referentiel $referentielRepository,
        RouterInterface $router,
        RequestStack $requestStack,
    ) {
        parent::__construct($entityManager, $translator, $repository, $pdf, $userProvider, $authorizationChecker);
        $this->questionRepository    = $questionRepository;
        $this->wordHandler           = $wordHandler;
        $this->authorizationChecker  = $authorizationChecker;
        $this->userProvider          = $userProvider;
        $this->maturityHandler       = $maturityHandler;
        $this->referentielRepository = $referentielRepository;
        $this->router                = $router;
        $this->requestStack          = $requestStack;
    }

    protected function getDomain(): string
    {
        return 'maturity';
    }

    protected function getModel(): string
    {
        return 'survey';
    }

    protected function getModelClass(): string
    {
        return Model\Survey::class;
    }

    protected function getFormType(): string
    {
        return SurveyType::class;
    }

    protected function getListData()
    {
        $order = [
            'createdAt' => 'DESC',
        ];

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->repository->findAll($order);
        }

        if ($this->authorizationChecker->isGranted('ROLE_REFERENT')) {
            $collectivities = \iterable_to_array($this->userProvider->getAuthenticatedUser()->getCollectivitesReferees());

            return $this->repository->findAllByCollectivities($collectivities, $order);
        }

        $data = $this->repository->findAllByCollectivity(
            $this->userProvider->getAuthenticatedUser()->getCollectivity(),
            $order
        );

        return $data;
    }

    /**
     * {@inheritdoc}
     * Here, we wanna compute maturity score.
     *
     * @param Model\Survey $object
     */
    public function formPrePersistData($object)
    {
        // Removed because this is done in App\Domain\Maturity\Symfony\EventSubscriber\Doctrine\GenerateMaturitySubscriber
        // $this->maturityHandler->handle($object);
    }

    /**
     * {@inheritdoc}
     * Override method in order to hydrate survey answers.
     */
    public function createMaturitySurveyAction(Request $request): Response
    {
        $object = new Model\Survey();

        /** @var Model\Referentiel $referentiel */
        $referentiel = $this->entityManager->getRepository(Model\Referentiel::class)->findOneBy([
            'id' => $request->get('referentiel'),
        ]);

        $object->setReferentiel($referentiel);

        $form = $this->createForm($this->getFormType(), $object);

        $form->setData(['referentiel' => $request->get('referentiel')]);

        $form->handleRequest($request);

        $answerSurveys = [];

        if ($form->isSubmitted()) {
            $data = $request->request->all();
            if (isset($data['survey']['questions'])) {
                foreach ($data['survey']['questions'] as $questionId => $question) {
                    if (isset($question['option'])) {
                        // Create new OptionalAnswer
                        $opa = new Model\OptionalAnswer();
                        $q   = $this->entityManager->getRepository(Model\Question::class)->find($questionId);
                        $opa->setQuestion($q);
                        $this->entityManager->persist($opa);
                        $object->addOptionalAnswer($opa);
                    } else {
                        foreach ($question['answers'] as $answerId) {
                            $answer = $this->entityManager->getRepository(Model\Answer::class)->find($answerId);
                            $as     = new Model\AnswerSurvey();
                            $as->setSurvey($object);
                            $as->setAnswer($answer);
                            $answerSurveys[] = $as;
                        }
                    }
                }
            }
            $object->setAnswerSurveys($answerSurveys);
            $this->formPrePersistData($object);
            $this->entityManager->persist($object);
            $this->entityManager->flush();

            $this->addFlash('success', $this->getFlashbagMessage('success', 'create', $object->__toString()));

            return $this->redirectToRoute($this->getRouteName('list'));
        }

        return $this->render($this->getTemplatingBasePath('create'), [
            'form' => $form->createView(),
        ]);
    }

    /**
     * {@inheritdoc}
     * Override method in order to hydrate survey answers.
     */
    public function editAction(Request $request, string $id): Response
    {
        /**
         * @var Model\Survey $object
         */
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        $toDelete = $this->entityManager->getRepository(Model\AnswerSurvey::class)->findBy(['survey' => $object]);

        $form = $this->createForm($this->getFormType(), $object);

        $form->setData(['referentiel' => $request->get('referentiel')]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data          = $request->request->all();
            $answerSurveys = [];
            if (isset($data['survey']['questions'])) {
                foreach ($data['survey']['questions'] as $questionId => $question) {
                    // Remove optional answer if one exists
                    $q              = $this->entityManager->getRepository(Model\Question::class)->find($questionId);
                    $optionalAnswer = $this->entityManager->getRepository(Model\OptionalAnswer::class)->findOneBy(['question' => $q, 'survey' => $object]);
                    if ($optionalAnswer) {
                        $this->entityManager->remove($optionalAnswer);
                    }
                    if (isset($question['option'])) {
                        // Create new OptionalAnswer
                        $opa = new Model\OptionalAnswer();

                        $opa->setQuestion($q);
                        $this->entityManager->persist($opa);
                        $object->addOptionalAnswer($opa);
                    } else {
                        foreach ($question['answers'] as $answerId) {
                            /** @var Model\Answer $answer */
                            $answer = $this->entityManager->getRepository(Model\Answer::class)->find($answerId);
                            $as     = $this->entityManager->getRepository(Model\AnswerSurvey::class)->findOneBy(['answer' => $answer, 'survey' => $object]);
                            if (!$as) {
                                $as = new Model\AnswerSurvey();
                            }

                            $as->setSurvey($object);
                            $as->setAnswer($answer);
                            $this->entityManager->persist($as);
                            $answerSurveys[] = $as;

                            $toDelete = array_filter($toDelete, function (Model\AnswerSurvey $asd) use ($as) {
                                return !$as->getId() || $as->getId() !== $asd->getId();
                            });
                        }
                    }
                }
            }

            foreach ($toDelete as $asd) {
                $this->entityManager->remove($asd);
            }
            $object->setAnswerSurveys($answerSurveys);
            $this->formPrePersistData($object);
            $this->entityManager->persist($object);
            $this->entityManager->flush();

            //            dd($object->getAnswerSurveys());

            $this->addFlash('success', $this->getFlashbagMessage('success', 'edit', $object->__toString()));

            return $this->redirectToRoute($this->getRouteName('list'));
        }

        return $this->render($this->getTemplatingBasePath('edit'), [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Generate a word report of survey.
     * Get current survey and previous one.
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public function reportAction(string $id): Response
    {
        $data        = [];
        $data['new'] = $this->repository->findOneById($id);

        $oldObjects = $this->repository->findPreviousById($id, 1);
        if (!empty($oldObjects)) {
            $data['old'] = $oldObjects[0];
        }

        return $this->wordHandler->generateMaturitySurveyReport($data);
    }

    public function startSurveyAction(Request $request)
    {
        if ($request->isMethod('GET')) {
            /** @var User $user */
            $user = $this->getUser();

            return $this->render($this->getTemplatingBasePath('start'), [
                'totalItem' => $this->referentielRepository->count(),
                'route'     => $this->router->generate('maturity_survey_referentiel_datatables', ['collectivity' => $user->getCollectivity()->getId()->toString()]),
            ]);
        }

        if (null === $referentiel = $this->referentielRepository->findOneById($request->request->get('referentiel_choice'))) {
            throw new NotFoundHttpException('No referentiel with Id ' . $request->request->get('referentiel_choice') . ' exists.');
        }

        return $this->redirectToRoute('maturity_survey_create', [
            'referentiel' => $referentiel->getId(),
        ]);
    }

    public function referentielsDatatablesAction()
    {
        $request      = $this->requestStack->getMasterRequest();
        $referentiels = $this->getReferentielResults($request);

        $collectivity = $this->entityManager->getRepository(Collectivity::class)->find($request->query->get('collectivity'));

        $reponse = $this->getBaseReferentielsDataTablesResponse($request, $referentiels);

        foreach ($referentiels as $referentiel) {
            /** @var Model\Referentiel $collectivityType */
            $collectivityType            = $collectivity->getType();
            $authorizedCollectivities    = $referentiel->getAuthorizedCollectivities();
            $authorizedCollectivityTypes = $referentiel->getAuthorizedCollectivityTypes();

            if ((!\is_null($authorizedCollectivityTypes)
                    && in_array($collectivityType, $authorizedCollectivityTypes))
                || $authorizedCollectivities->contains($collectivity)
            ) {
                $reponse['data'][] = [
                    'name'        => '<label class="required" for="' . $referentiel->getId() . '" style="font-weight:normal;"><input type="radio" id="' . $referentiel->getId() . '" value="' . $referentiel->getId() . '" name="referentiel_choice" required="true"/> ' . $referentiel->getName() . '</label>',
                    'description' => $referentiel->getDescription(),
                ];
            }
        }

        $reponse['recordsTotal']    = count($reponse['data']);
        $reponse['recordsFiltered'] = count($reponse['data']);

        return new JsonResponse($reponse);
    }

    protected function getBaseReferentielsDataTablesResponse(Request $request, $results, array $criteria = [])
    {
        $draw = $request->request->get('draw');

        $reponse = [
            'draw'            => $draw,
            'recordsTotal'    => $this->referentielRepository->count($criteria),
            'recordsFiltered' => count($results),
            'data'            => [],
        ];

        return $reponse;
    }

    protected function getReferentielResults(Request $request): ?Paginator
    {
        $first      = $request->request->get('start');
        $maxResults = $request->request->get('length');
        $orders     = $request->request->get('order');
        $columns    = $request->request->get('columns');

        $orderColumn = $this->getCorrespondingLabelFromkeyForReferentiels($orders[0]['column']);
        $orderDir    = $orders[0]['dir'];

        $searches = [];
        foreach ($columns as $column) {
            if ('' !== $column['search']['value']) {
                $searches[$column['data']] = $column['search']['value'];
            }
        }

        return $this->referentielRepository->findPaginated($first, $maxResults, $orderColumn, $orderDir, $searches);
    }

    private function getCorrespondingLabelFromkeyForReferentiels(string $key)
    {
        return \array_key_exists($key, $this->getLabelAndKeysArrayForReferentiels()) ? $this->getLabelAndKeysArrayForReferentiels()[$key] : null;
    }

    private function getLabelAndKeysArrayForReferentiels()
    {
        return [
            '0' => 'name',
            '1' => 'description',
        ];
    }

    public function listAction(): Response
    {
        $surveys      = $this->repository->findAll();
        $referentiels = [];
        foreach ($surveys as $survey) {
            $referentiels[] = $survey->getReferentiel()->getName();
        }
        $criteria = [];
        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $criteria['collectivity'] = $this->userProvider->getAuthenticatedUser()->getCollectivity();
        }

        $category = $this->entityManager->getRepository(Category::class)->findOneBy([
            'name' => 'Indice de maturité',
        ]);

        return $this->render('Maturity/Survey/list.html.twig', [
            'category'     => $category,
            'totalItem'    => $this->repository->count($criteria),
            'route'        => $this->router->generate('maturity_survey_list_datatables'),
            'referentiels' => array_unique($referentiels, SORT_STRING),
        ]);
    }

    public function listDataTables(Request $request): JsonResponse
    {
        $criteria = [];
        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $criteria['collectivity'] = $this->userProvider->getAuthenticatedUser()->getCollectivity();
        }
        $surveys = $this->getResults($request, $criteria);
        $reponse = $this->getBaseDataTablesResponse($request, $surveys);

        foreach ($surveys as $survey) {
            $referentielLink = '<a aria-label="' . \htmlspecialchars($survey->getReferentiel()->getName()) . '" href="' . $this->router->generate('maturity_survey_synthesis', ['id' => $survey->getId()->toString()]) . '">
                ' . \htmlspecialchars($survey->getReferentiel()->getName()) . '
                </a>';

            $reponse['data'][] = [
                'collectivity' => $survey->getCollectivity()->getName(),
                'referentiel'  => $referentielLink,
                'score'        => $survey->getScore() / 10,
                'createdAt'    => date_format($survey->getCreatedAt(), 'd-m-Y H:i'),
                'updatedAt'    => date_format($survey->getUpdatedAt(), 'd-m-Y H:i'),
                'actions'      => $this->generateActionCellContent($survey),
            ];
        }
        $reponse['recordsTotal'] = $this->repository->count($criteria);

        return new JsonResponse($reponse);
    }

    private function generateActionCellContent(Model\Survey $survey)
    {
        $id = $survey->getId();

        return
            '<a href="' . $this->router->generate('maturity_survey_report', ['id' => $id]) . '">
                <i class="fa fa-print"></i> '
            . $this->translator->trans('action.print') .
            '</a>' .
            '<a href="' . $this->router->generate('maturity_survey_synthesis', ['id' => $id]) . '">
                <i class="fa fa-chart-bar"></i> ' .
            $this->translator->trans('action.synthesis') .
            '</a>' .
            '<a href="' . $this->router->generate('maturity_survey_edit', ['id' => $id]) . '">
                <i class="fa fa-pencil-alt"></i> '
            . $this->translator->trans('action.edit') .
            '</a>' .
            '<a href="' . $this->router->generate('maturity_survey_delete', ['id' => $id]) . '">
                <i class="fa fa-trash"></i> ' .
            $this->translator->trans('action.delete') .
            '</a>';
    }

    protected function getLabelAndKeysArray(): array
    {
        if ($this->isGranted('ROLE_REFERENT')) {
            return [
                'referentiel',
                'collectivity',
                'score',
                'createdAt',
                'updatedAt',
                'actions',
            ];
        }

        return [
            'referentiel',
            'score',
            'createdAt',
            'updatedAt',
            'actions',
        ];
    }

    public function syntheseAction(Request $request, string $id): Response
    {
        //        /** @var CollectivityRelated $object */
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        $previous = $this->repository->findPreviousById($id);

        $serviceEnabled = false;

        if ($object instanceof Collectivity) {
            $serviceEnabled = $object->getIsServicesEnabled();
        } elseif ($object instanceof CollectivityRelated) {
            $serviceEnabled = $object->getCollectivity()->getIsServicesEnabled();
        }

        /**
         * @var User $user
         */
        $user = $this->getUser();

        $actionEnabled = true;
        if ($object instanceof CollectivityRelated && (!$this->authorizationChecker->isGranted('ROLE_ADMIN') && !$user->getServices()->isEmpty())) {
            $actionEnabled = $object->isInUserServices($this->userProvider->getAuthenticatedUser());
        }

        if (!$actionEnabled) {
            return $this->redirectToRoute($this->getRouteName('list'));
        }

        $form = $this->createForm(SyntheseType::class, $object);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($object);
            $this->entityManager->flush();

            $this->addFlash('success', $this->getFlashbagMessage('success', 'edit', $object));

            return $this->redirectToRoute($this->getRouteName('list'));
        }

        return $this->render($this->getTemplatingBasePath('synthese'), [
            'form'     => $form->createView(),
            'object'   => $object,
            'previous' => $previous,
        ]);
    }
}
