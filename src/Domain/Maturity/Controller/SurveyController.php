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
use App\Application\Symfony\Security\UserProvider;
use App\Domain\Maturity\Calculator\MaturityHandler;
use App\Domain\Maturity\Form\Type\SurveyType;
use App\Domain\Maturity\Model;
use App\Domain\Maturity\Repository;
use App\Domain\Reporting\Handler\WordHandler;
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

    private Repository\Referentiel  $referentielRepository;
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

    /**
     * {@inheritdoc}
     */
    protected function getDomain(): string
    {
        return 'maturity';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModel(): string
    {
        return 'survey';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\Survey::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormType(): string
    {
        return SurveyType::class;
    }

    /**
     * {@inheritdoc}
     */
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
        $this->maturityHandler->handle($object);
        foreach($object->getMaturity() as $m) {
            $this->entityManager->persist($m);
        }
    }

    /**
     * {@inheritdoc}
     * Override method in order to hydrate survey answers.
     */
    public function createMaturitySurveyAction(Request $request): Response
    {
        /**
         * @var Model\Survey $object
         */
        $modelClass = $this->getModelClass();
        $object     = new $modelClass();

        /** @var Model\Referentiel $referentiel */
        $referentiel = $this->entityManager->getRepository(Model\Referentiel::class)->findOneBy([
            'id' => $request->get('referentiel'),
        ]);

        $object->setReferentiel($referentiel);

        $form = $this->createForm($this->getFormType(), $object);

        $form->setData(['referentiel' => $request->get('referentiel')]);

        $form->handleRequest($request);

        if($form->isSubmitted()) {
            $data = $request->request->all();
            foreach ($data['survey']['questions'] as $questionId => $question) {
                foreach ($question['answers'] as $answerId) {
                    $answer = $this->entityManager->getRepository(Model\Answer::class)->find($answerId);
                    $object->addAnswer($answer);
                }
            }

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

        $form = $this->createForm($this->getFormType(), $object);

        $form->setData(['referentiel' => $request->get('referentiel')]);

        $form->handleRequest($request);

        if($form->isSubmitted()) {
            $this->formPrePersistData($object);

            foreach ($object->getAnswers() as $a) {
                $object->removeAnswer($a);
                $a->setSurveys([]);
            }
            $object->setAnswers([]);

            $data = $request->request->all();
            foreach ($data['survey']['questions'] as $questionId => $question) {
                foreach ($question['answers'] as $answerId) {
                    /** @var Model\Answer $answer */
                    $answer = $this->entityManager->getRepository(Model\Answer::class)->find($answerId);
                    $object->addAnswer($answer);
                }
            }

            //dd('dead');
            $this->entityManager->persist($object);
            $this->entityManager->flush();

            $this->addFlash('success', $this->getFlashbagMessage('success', 'update', $object->__toString()));

            return $this->redirectToRoute($this->getRouteName('list'));
        }


        return $this->render($this->getTemplatingBasePath('create'), [
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
            return $this->render($this->getTemplatingBasePath('start'), [
                'totalItem' => $this->referentielRepository->count(),
                'route'     => $this->router->generate('maturity_survey_referentiel_datatables'),
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

        $reponse = $this->getBaseDataTablesResponse($request, $referentiels);
        foreach ($referentiels as $referentiel) {
            $reponse['data'][] = [
                'nom'         => '<input type="radio" value="' . $referentiel->getId() . '" name="referentiel_choice" required="true"/> ' . $referentiel->getName(),
                'description' => $referentiel->getDescription(),
            ];
        }
        $reponse['recordsTotal']    = count($reponse['data']);
        $reponse['recordsFiltered'] = count($reponse['data']);

        return new JsonResponse($reponse);
    }

    protected function getBaseDataTablesResponse(Request $request, $results, array $criteria = [])
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
            '0' => 'nom',
            '1' => 'description',
        ];
    }
}
