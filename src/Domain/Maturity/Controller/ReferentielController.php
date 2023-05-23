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
use App\Application\Traits\ServersideDatatablesTrait;
use App\Domain\Maturity\Form\Type\ModeleReferentielRightsType;
use App\Domain\Maturity\Form\Type\ReferentielType;
use App\Domain\Maturity\Model;
use App\Domain\Maturity\Repository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property Repository\Referentiel $repository
 */
class ReferentielController extends CRUDController
{
    use ServersideDatatablesTrait;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var UserProvider
     */
    protected $userProvider;

    protected RouterInterface $router;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\Referentiel $repository,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProvider $userProvider,
        RouterInterface $router,
        Pdf $pdf,
    ) {
        parent::__construct($entityManager, $translator, $repository, $pdf, $userProvider, $authorizationChecker);
        $this->authorizationChecker = $authorizationChecker;
        $this->userProvider         = $userProvider;
        $this->router               = $router;
    }

    protected function getDomain(): string
    {
        return 'maturity';
    }

    protected function getModel(): string
    {
        return 'referentiel';
    }

    protected function getModelClass(): string
    {
        return Model\Referentiel::class;
    }

    protected function getFormType(): string
    {
        return ReferentielType::class;
    }

    /**
     * {@inheritdoc}
     * Override method in order to hydrate survey answers.
     */
    public function createAction(Request $request): Response
    {
        /**
         * @var Model\Referentiel
         */
        $modelClass = $this->getModelClass();
        $object     = new $modelClass();

        $form = $this->createForm($this->getFormType(), $object);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->formPrePersistData($object);
            $this->entityManager->persist($object);
            $this->entityManager->flush();

            $this->addFlash('success', $this->getFlashbagMessage('success', 'create', $object->getName()));

            return $this->redirectToRoute($this->getRouteName('list'));
        }

        return $this->render($this->getTemplatingBasePath('create'), [
            'form' => $form->createView(),
        ]);
    }

    public function editAction(Request $request, string $id): Response
    {
        /** @var Model\Referentiel $object */
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        $sections = $this->entityManager->getRepository(Model\Domain::class)->findBy(['referentiel' => $object]);

        $form = $this->createForm($this->getFormType(), $object, ['validation_groups' => ['default', $this->getModel(), 'edit']]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->formPrePersistData($object);
            $this->entityManager->persist($object);
            $this->entityManager->flush();

            $this->addFlash('success', $this->getFlashbagMessage('success', 'edit', $object));

            return $this->redirectToRoute($this->getRouteName('list'));
        }

        return $this->render($this->getTemplatingBasePath('edit'), [
            'form' => $form->createView(),
        ]);
    }

    public function duplicateAction(Request $request, string $id): Response
    {
        /** @var Model\Referentiel $object */
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        $newRef = new Model\Referentiel();

        $newRef->setName($object->getName());
        $newRef->setDescription($object->getDescription());

        $newRef->setAuthorizedCollectivities($object->getAuthorizedCollectivities());

        $newRef->setAuthorizedCollectivityTypes($object->getAuthorizedCollectivityTypes());
        $newRef->setOptionRightSelection($newRef->getOptionRightSelection());

        /** @var Model\Domain $domain */
        foreach ($object->getDomains() as $domain) {
            $d = new Model\Domain();
            $d->setName($domain->getName());
            $d->setDescription($domain->getDescription());
            $d->setReferentiel($newRef);
            $d->setColor($domain->getColor());
            $d->setPosition($domain->getPosition());

            $this->entityManager->persist($d);
            /** @var Model\Question $q */
            foreach ($domain->getQuestions() as $q) {
                $newQ = new Model\Question();
                $newQ->setPosition($q->getPosition());
                $newQ->setName($q->getName());
                $newQ->setOption($q->getOption());
                $newQ->setOptionReason($q->getOptionReason());
                $newQ->setWeight($q->getWeight());
                $newQ->setDomain($d);
                $this->entityManager->persist($newQ);
                /** @var Model\Answer $a */
                foreach ($q->getAnswers() as $a) {
                    $newA = new Model\Answer();
                    $newA->setName($a->getName());
                    $newA->setPosition($a->getPosition());
                    $newA->setRecommendation($a->getRecommendation());
                    $newA->setResponse($a->getResponse());
                    $newA->setQuestion($newQ);
                    $this->entityManager->persist($newA);
                }
            }
        }

        $this->entityManager->persist($newRef);
        $this->entityManager->flush();

        $this->addFlash('success', $this->getFlashbagMessage('success', 'duplicate', $object));

        return $this->redirectToRoute($this->getRouteName('edit'), ['id' => $newRef->getId()->toString()]);
    }

    public function formPrePersistData($object)
    {
        $domains = [];

        $colors = [
            'info',
            'success',
            'primary',
            'warning',
        ];

        // get all existing domains
        $toRemove = $this->entityManager->getRepository(Model\Domain::class)->findBy(['referentiel' => $object]);

        foreach ($object->getDomains() as $k => $domain) {
            $key = array_search($domain, $toRemove);
            if (false !== $key) {
                unset($toRemove[$key]);
            }
            /** @var Model\Domain $domain */
            if (is_null($domain->getPosition())) {
                $domain->setPosition($k);
            }
            if (is_null($domain->getColor())) {
                $domain->setColor($colors[$k % 4]);
            }

            // get all existing questions
            $toRemoveQuestions = $this->entityManager->getRepository(Model\Question::class)->findBy(['domain' => $domain]);

            $questions = [];
            foreach ($domain->getQuestions() as $n => $question) {
                /** @var Model\Question $question */
                $key = array_search($question, $toRemoveQuestions);
                if (false !== $key) {
                    unset($toRemoveQuestions[$key]);
                }
                if (is_null($question->getPosition())) {
                    $question->setPosition($n);
                }
                $question->setDomain($domain);

                // get all existing Answers
                $toRemoveAnswers = $this->entityManager->getRepository(Model\Answer::class)->findBy(['question' => $question]);

                foreach ($question->getAnswers() as $l => $answer) {
                    /** @var Model\Answer $answer */
                    $key = array_search($answer, $toRemoveAnswers);
                    if (false !== $key) {
                        unset($toRemoveAnswers[$key]);
                    }
                    if (is_null($answer->getPosition())) {
                        $answer->setPosition($l);
                    }
                    $answer->setQuestion($question);
                }

                foreach ($toRemoveAnswers as $removing) {
                    $this->entityManager->remove($removing);
                }

                $questions[] = $question;
            }
            foreach ($toRemoveQuestions as $removing) {
                $this->entityManager->remove($removing);
            }
            $domain->setQuestions($questions);
            $domain->setReferentiel($object);

            $domains[] = $domain;
        }

        // Remove deleted domains
        foreach ($toRemove as $removing) {
            $this->entityManager->remove($removing);
        }

        $object->setDomains($domains);
    }

    /**
     * The list action view
     * Get data & display them.
     */
    public function listAction(): Response
    {
        return $this->render('Maturity/Referentiel/list.html.twig', [
            'totalItem' => $this->repository->count(),
            'route'     => $this->router->generate('maturity_referentiel_list_datatables'),
        ]);
    }

    public function listDataTables(Request $request): JsonResponse
    {
        $referentiels = $this->getResults($request);
        $reponse      = $this->getBaseDataTablesResponse($request, $referentiels);

        foreach ($referentiels as $referentiel) {
            $reponse['data'][] = [
                'name'        => $referentiel->getName(),
                'description' => $referentiel->getDescription(),
                'createdAt'   => date_format($referentiel->getCreatedAt(), 'd-m-Y'),
                'updatedAt'   => date_format($referentiel->getUpdatedAt(), 'd-m-Y'),
                'actions'     => $this->generateActionCellContent($referentiel),
            ];
        }

        $reponse['recordsTotal']    = count($reponse['data']);
        $reponse['recordsFiltered'] = count($reponse['data']);

        return new JsonResponse($reponse);
    }

    private function generateActionCellContent(Model\Referentiel $referentiel)
    {
        $id                  = $referentiel->getId();
        $htmltoReturnIfAdmin = '';

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $htmltoReturnIfAdmin = '<a href="' . $this->router->generate('maturity_referentiel_rights', ['id' => $id]) . '">
                <i class="fa fa-user-shield"></i>'
                . $this->translator->trans('action.rights') .
                '</a>';
        }

        return
            '<a href="' . $this->router->generate('maturity_referentiel_edit', ['id' => $id]) . '">
                <i class="fa fa-pencil-alt"></i>'
            . $this->translator->trans('action.edit') .
            '</a>'
            . $htmltoReturnIfAdmin .
            '<a href="' . $this->router->generate('maturity_referentiel_duplicate', ['id' => $id]) . '">
                <i class="fa fa-clone"></i>' .
            $this->translator->trans('action.duplicate') .
            '</a>' .
            '<a href="' . $this->router->generate('maturity_referentiel_edit', ['id' => $id]) . '">
                <i class="fa fa-file-code"></i>' .
            $this->translator->trans('action.export') .
            '</a>' .
            '<a href="' . $this->router->generate('maturity_referentiel_delete', ['id' => $id]) . '">
                <i class="fa fa-trash"></i>' .
            $this->translator->trans('action.delete') .
            '</a>';
    }

    protected function getLabelAndKeysArray(): array
    {
        return [
            '0' => 'name',
            '1' => 'description',
            '2' => 'createdAt',
            '3' => 'updatedAt',
            '4' => 'actions',
        ];
    }

    public function rightsAction(Request $request, string $id): Response
    {
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }
        $form = $this->createForm(ModeleReferentielRightsType::class, $object);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->formPrePersistData($object);
            $this->entityManager->persist($object);
            $this->entityManager->flush();

            $this->addFlash('success', $this->getFlashbagMessage('success', 'rights', $object));

            return $this->redirectToRoute($this->getRouteName('list'));
        }

        return $this->render('Maturity/Referentiel/rights.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
