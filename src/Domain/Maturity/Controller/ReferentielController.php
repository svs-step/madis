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
use App\Domain\Documentation\Model\Category;
use App\Domain\Maturity\Calculator\MaturityHandler;
use App\Domain\Maturity\Form\Type\ReferentielType;
use App\Domain\Maturity\Model;
use App\Domain\Maturity\Repository;
use App\Domain\Reporting\Handler\WordHandler;
use App\Domain\User\Repository\Collectivity;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Application\Traits\ServersideDatatablesTrait;

/**
 * @property Repository\Referentiel $repository
 */
class ReferentielController extends CRUDController
{
    use ServersideDatatablesTrait;
    /**
     * @var WordHandler
     */
    private $wordHandler;

    /**
     * @var Collectivity
     */
    protected $collectivityRepository;

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

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\Referentiel $repository,
        WordHandler $wordHandler,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProvider $userProvider,
        MaturityHandler $maturityHandler,
        RouterInterface $router,
        Pdf $pdf,
        Collectivity $collectivityRepository,
    ) {
        parent::__construct($entityManager, $translator, $repository, $pdf, $userProvider, $authorizationChecker);
        $this->wordHandler          = $wordHandler;
        $this->authorizationChecker = $authorizationChecker;
        $this->userProvider         = $userProvider;
        $this->maturityHandler      = $maturityHandler;
        $this->router                 = $router;
        $this->collectivityRepository = $collectivityRepository;
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
        return 'referentiel';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\Referentiel::class;
    }

    /**
     * {@inheritdoc}
     */
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
            $em = $this->getDoctrine()->getManager();
            $em->persist($object);
            $em->flush();

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

        $sections =  $this->entityManager->getRepository(Model\ReferentielSection::class)->findBy(['referentiel' => $object]);

        // Before create form, hydrate new answers array with potential question responses
        foreach ($sections as $section) {
            $questions =  $this->entityManager->getRepository(Model\ReferentielQuestion::class)->findBy(['referentielSection' => $section]);
            foreach ($questions as $question){
                $answers = $this->entityManager->getRepository(Model\ReferentielAnswer::class)->findBy(['referentielQuestion' => $question]);
                foreach($answers as $answer){
                    $question->addReferentielAnswer($answer);
                }
                $section->addReferentielQuestion($question);
            }
            $object->addReferentielSection($section);
        }

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
            'form'           => $form->createView(),
        ]);
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
        $reponse = $this->getBaseDataTablesResponse($request, $referentiels);

        foreach ($referentiels as $referentiel) {
            $reponse['data'][] = [
                'name'         => $referentiel->getName(),
                'description' => $referentiel->getDescription(),
                'createdAt'   => date_format($referentiel->getCreatedAt(), 'd-m-Y'),
                'updatedAt'   => date_format($referentiel->getUpdatedAt(), 'd-m-Y'),
                'actions'     => $this->generateActioNCellContent($referentiel),
            ];
        }

        $reponse['recordsTotal']    = count($reponse['data']);
        $reponse['recordsFiltered'] = count($reponse['data']);

        return new JsonResponse($reponse);
    }

    private function generateActioNCellContent(Model\Referentiel $referentiel)
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
            '<a href="' . $this->router->generate('maturity_referentiel_edit', ['id' => $id]) . '">
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
        $form = $this->createForm(ModeleAnalyseRightsType::class, $object);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->formPrePersistData($object);
            $this->entityManager->persist($object);
            $this->entityManager->flush();

            $this->addFlash('success', $this->getFlashbagMessage('success', 'rights', $object));

            return $this->redirectToRoute($this->getRouteName('list'));
        }

        return $this->render('Aipd/Modele_analyse/rights.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
