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
use App\Application\Traits\ServersideDatatablesTrait;
use App\Domain\Documentation\Model\Category;
use App\Domain\Registry\Calculator\Completion\ConformiteTraitementCompletion;
use App\Domain\Registry\Dictionary\ConformiteTraitementLevelDictionary;
use App\Domain\Registry\Dictionary\TreatmentAuthorDictionary;
use App\Domain\Registry\Dictionary\TreatmentLegalBasisDictionary;
use App\Domain\Registry\Form\Type\TreatmentConfigurationType;
use App\Domain\Registry\Form\Type\TreatmentType;
use App\Domain\Registry\Model;
use App\Domain\Registry\Model\PublicConfiguration;
use App\Domain\Registry\Model\Treatment;
use App\Domain\Registry\Repository;
use App\Domain\Reporting\Handler\WordHandler;
use App\Domain\User\Dictionary\UserRoleDictionary;
use App\Domain\User\Model as UserModel;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Repository as UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property Repository\Treatment $repository
 */
class TreatmentController extends CRUDController
{
    use ServersideDatatablesTrait;

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
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\Treatment $repository,
        UserRepository\Collectivity $collectivityRepository,
        RequestStack $requestStack,
        WordHandler $wordHandler,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProvider $userProvider,
        Pdf $pdf,
        RouterInterface $router
    ) {
        parent::__construct($entityManager, $translator, $repository, $pdf, $userProvider, $authorizationChecker);
        $this->collectivityRepository = $collectivityRepository;
        $this->requestStack           = $requestStack;
        $this->wordHandler            = $wordHandler;
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
    public function listAction(): Response
    {
        $request            = $this->requestStack->getMasterRequest();
        $criteria['active'] = 'true' === $request->query->get('active') || \is_null($request->query->get('active'))
            ? true
            : false
        ;

        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $criteria['collectivity'] = $this->userProvider->getAuthenticatedUser()->getCollectivity();
        }

        $category = $this->entityManager->getRepository(Category::class)->findOneBy([
            'name' => 'Traitement',
        ]);

        return $this->render('Registry/Treatment/list.html.twig', [
            'totalItem' => $this->repository->count($criteria),
            'category'  => $category,
            'route'     => $this->router->generate('registry_treatment_list_datatables', ['active' => $criteria['active']]),
        ]);
    }

    /**
     * Generate a word report of contractors.
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
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
     * {@inheritdoc}
     */
    public function configurationAction(): Response
    {
        $request            = $this->requestStack->getMasterRequest();
        $criteria['active'] = 'true' === $request->query->get('active') || \is_null($request->query->get('active'))
            ? true
            : false
        ;

        $configuration = $this
            ->getDoctrine()
            ->getRepository(PublicConfiguration::class)
            // find by type
            ->findOneBy(['type' => Treatment::class]);

        if (!$configuration) {
            $configuration = new PublicConfiguration(Treatment::class);
        }

        $form = $this->createForm(TreatmentConfigurationType::class, $configuration);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();
        }

        return $this->render('Registry/Treatment/configuration.html.twig', [
            'route' => $this->router->generate('registry_treatment_configuration', ['active' => $criteria['active']]),
            'form'  => $form->createView(),
        ]);
    }

    /**
     * The list public action view
     * Get collectivity treatments & display them.
     */
    public function publicListAction(string $id): Response
    {
        $collectivity = $this
            ->getDoctrine()
            ->getRepository(Collectivity::class)
            ->find($id);

        $data = $this
        ->getDoctrine()
        ->getRepository(Treatment::class)
        ->findBy(
            [
                'collectivity' => $collectivity,
                'public'       => 1,
                'active'       => 1,
            ],
            [
                'name' => 'ASC',
            ],
        );

        $objects = [];

        foreach ($data as $treatment) {
            if (true == $treatment->getPublic()) {
                $objects[] = $treatment;
            }
        }

        return $this->render($this->getTemplatingBasePath('public_list'), [
            'objects'      => $objects,
            'route'        => '/public/traitements/datatables?active=1',
            'totalItem'    => count($objects),
            'collectivity' => $collectivity,
        ]);
    }

    /**
     * The public show action view
     * Display the public information of the object.
     *
     * @param string $id The ID of the treatment to display
     */
    public function publicShowAction(string $id): Response
    {
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        $configurationEntity = $this
            ->getDoctrine()
            ->getRepository(PublicConfiguration::class)
            // find by type
            ->findOneBy(['type' => Treatment::class]);

        if ($configurationEntity) {
            $configuration = json_decode($configurationEntity->getSavedConfiguration(), true);
        } else {
            $configuration = new PublicConfiguration(Treatment::class);
        }

        return $this->render($this->getTemplatingBasePath('public_show'), [
            'object' => $object,
            'config' => $configuration,
        ]);
    }

    /**
     * Get all active treatments of a collectivity and return their id/name as JSON.
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

    /**
     * {@inheritdoc}
     */
    public function listDataTables(Request $request): JsonResponse
    {
        $request            = $this->requestStack->getMasterRequest();
        $criteria['active'] = $request->query->getBoolean('active');
        $user               = $this->userProvider->getAuthenticatedUser();

        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $criteria['collectivity'] = $user->getCollectivity();
        }

        if ($user) {
            if (\in_array(UserRoleDictionary::ROLE_REFERENT, $user->getRoles())) {
                $criteria['collectivity'] = $user->getCollectivitesReferees();
            }
        }

        /** @var Paginator $treatments */
        $treatments = $this->getResults($request, $criteria);

        $reponse = $this->getBaseDataTablesResponse($request, $treatments, $criteria);

        /** @var Model\Treatment $treatment */
        foreach ($treatments as $treatment) {
            if (!$this->authorizationChecker->isGranted('IS_AUTHENTICATED_ANONYMOUSLY')) {
                $treatmentLink = '<a href="' . $this->router->generate('registry_public_treatment_show', ['id' => $treatment->getId()->toString()]) . '">
                ' . \htmlspecialchars($treatment->getName()) . '
                </a>';
            } else {
                $treatmentLink = '<a href="' . $this->router->generate('registry_treatment_show', ['id' => $treatment->getId()->toString()]) . '">
                ' . \htmlspecialchars($treatment->getName()) . '
                </a>';
            }

            $contractors = '<ul>';
            foreach ($treatment->getContractors() as $contractor) {
                $contractors .= '<li>' . $contractor->getName() . '</li>';
            }
            $contractors .= '</ul>';

            $yes = '<span class="badge bg-green">' . $this->translator->trans('label.yes') . '</span>';
            $no  = '<span class="badge bg-orange">' . $this->translator->trans('label.no') . '</span>';

            $reponse['data'][] = [
                'id'                     => $treatment->getId(),
                'nom'                    => $treatmentLink,
                'collectivite'           => $this->authorizationChecker->isGranted('ROLE_REFERENT') ? $treatment->getCollectivity()->getName() : '',
                'baseLegal'              => !empty($treatment->getLegalBasis()) && array_key_exists($treatment->getLegalBasis(), TreatmentLegalBasisDictionary::getBasis()) ? TreatmentLegalBasisDictionary::getBasis()[$treatment->getLegalBasis()] : $treatment->getLegalBasis(),
                'logiciel'               => $treatment->getSoftware(),
                'enTantQue'              => !empty($treatment->getAuthor()) && array_key_exists($treatment->getAuthor(), TreatmentAuthorDictionary::getAuthors()) ? TreatmentAuthorDictionary::getAuthors()[$treatment->getAuthor()] : $treatment->getAuthor(),
                'gestionnaire'           => $treatment->getManager(),
                'sousTraitant'           => $contractors,
                'controleAcces'          => $treatment->getSecurityAccessControl()->isCheck() ? $yes : $no,
                'tracabilite'            => $treatment->getSecurityTracability()->isCheck() ? $yes : $no,
                'saving'                 => $treatment->getSecuritySaving()->isCheck() ? $yes : $no,
                'update'                 => $treatment->getSecurityUpdate()->isCheck() ? $yes : $no,
                'other'                  => $treatment->getSecurityOther()->isCheck() ? $yes : $no,
                'entitledPersons'        => $treatment->isSecurityEntitledPersons() ? $yes : $no,
                'openAccounts'           => $treatment->isSecurityOpenAccounts() ? $yes : $no,
                'specificitiesDelivered' => $treatment->isSecuritySpecificitiesDelivered() ? $yes : $no,
                'updatedAt'              => date_format($treatment->getUpdatedAt(), 'd-m-Y H:i:s'),
                'public'                 => $treatment->getPublic() ? $yes : $no,
                'responsableTraitement'  => $treatment->getCoordonneesResponsableTraitement(),
                'specific_traitement'    => $this->getSpecificTraitement($treatment),
                'conformite_traitement'  => $this->getTreatmentConformity($treatment),
                'actions'                => $this->generateActionCellContent($treatment),
            ];
        }

        return new JsonResponse($reponse);
    }

    private function getTreatmentConformity(Treatment $treatment)
    {
        if (!$treatment->getConformiteTraitement()) {
            return '<span class="label label-default" style="min-width: 100%; display: inline-block;">Non évalué</span>';
        }
        $conf  = $treatment->getConformiteTraitement();
        $level = ConformiteTraitementCompletion::getConformiteTraitementLevel($conf);

        $weight = ConformiteTraitementLevelDictionary::getConformitesWeight()[$level];

        switch ($weight) {
            case 1:
                $label = 'Conforme';
                $class = 'label-success';
                break;
            case 2:
                $label = 'Non-conforme mineure';
                $class = 'label-warning';
                break;
            default:
                $label = 'Non-conforme majeure';
                $class = 'label-danger';
        }

        return '<span class="label ' . $class . '" style="min-width: 100%; display: inline-block;">' . $label . '</span>';
    }

    private function getSpecificTraitement(Treatment $treatment)
    {
        $user   = $this->userProvider->getAuthenticatedUser();
        $values = [];
        if ($treatment->isSystematicMonitoring()) {
            array_push($values, $this->translator->trans('registry.treatment.show.systematic_monitoring'));
        }
        if ($treatment->isLargeScaleCollection()) {
            array_push($values, $this->translator->trans('registry.treatment.show.large_scale_collection'));
        }
        if ($treatment->isVulnerablePeople()) {
            array_push($values, $this->translator->trans('registry.treatment.show.vulnerable_people'));
        }
        if ($treatment->isDataCrossing()) {
            array_push($values, $this->translator->trans('registry.treatment.show.data_crossing'));
        }
        if ($treatment->isEvaluationOrRating()) {
            array_push($values, $this->translator->trans('registry.treatment.show.evaluation_or_rating'));
        }
        if ($treatment->isAutomatedDecisionsWithLegalEffect()) {
            array_push($values, $this->translator->trans('registry.treatment.show.automated_decisions_with_legal_effect'));
        }
        if ($treatment->isAutomaticExclusionService()) {
            array_push($values, $this->translator->trans('registry.treatment.show.automatic_exclusion_service'));
        }
        if ($treatment->isInnovativeUse()) {
            array_push($values, $this->translator->trans('registry.treatment.show.innovative_use'));
        }

        return $values;
    }

    private function isTreatmentInUserServices(Model\Treatment $treatment): bool
    {
        $user = $this->userProvider->getAuthenticatedUser();
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return $treatment->isInUserServices($user);
    }

    private function generateActionCellContent(Model\Treatment $treatment)
    {
        $id = $treatment->getId();

        $user = $this->userProvider->getAuthenticatedUser();
        if ($user->getServices()->isEmpty() || $this->isTreatmentInUserServices($treatment)) {
            $editPath   = $this->router->generate('registry_treatment_edit', ['id' => $id]);
            $deletePath = $this->router->generate('registry_treatment_delete', ['id' => $id]);

            if ($this->authorizationChecker->isGranted('ROLE_USER')) {
                return '<a href="' . $editPath . '">
             <i class="fa fa-pencil-alt"></i>
                 ' . $this->translator->trans('action.edit') . '
             </a>
             <a href="' . $deletePath . '">
                 <i class="fa fa-trash"></i>
                 ' . $this->translator->trans('action.delete') . '
             </a>'
                ;
            }
        }

        return null;
    }

    public function pdfAllAction()
    {
        $request = $this->requestStack->getMasterRequest();
        $ids     = $request->query->get('ids');
        $ids     = explode(',', $ids);

        $objects = [];

        foreach ($ids as $id) {
            $treatment = $this->repository->findOneById($id);
            array_push($objects, $treatment);
        }

        return new PdfResponse(
            $this->pdf->getOutputFromHtml(
                $this->renderView($this->getTemplatingBasePath('pdf_all'), ['objects' => $objects])
            ),
            $this->getPdfName((string) 'print') . '.pdf'
        );
    }

    /**
     * The archive action view
     * Display a confirmation message to confirm data archivation.
     */
    public function archiveAllAction(): Response
    {
        $request = $this->requestStack->getMasterRequest();
        $ids     = $request->query->get('ids');
        $ids     = explode(',', $ids);

        if (!$this->authorizationChecker->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer ces traitements');

            return $this->redirectToRoute($this->getRouteName('list'));
        }

        return $this->render($this->getTemplatingBasePath('archive_all'), [ // delete_all
            'ids'              => $ids,
            'treatment_length' => count($ids),
        ]);
    }

    /**
     * The archive action
     * Display a confirmation message to confirm data archived.
     */
    public function archiveConfirmationAction(): Response
    {
        $request = $this->requestStack->getMasterRequest();
        $ids     = $request->query->get('ids');

        if (!$this->authorizationChecker->isGranted('ROLE_USER')) {
            // $this->addFlash('success', $this->getFlashbagMessage('success', 'delete'));
            $this->addFlash('error', 'Vous ne pouvez pas archiver ces traitements');

            return $this->redirectToRoute($this->getRouteName('list'));
        }

        foreach ($ids as $id) {
            /**
             * @var Treatment
             */
            $treatment = $this->repository->findOneById($id);
            $user      = $this->getUser();
            if ($treatment
                && (
                    $user instanceof UserModel\User
                    && $treatment->getCollectivity() === $user->getCollectivity()
                    && (0 === count($user->getServices()) || in_array($treatment->getService(), $user->getServices()->toArray()))
                )
                || $this->authorizationChecker->isGranted('ROLE_REFERENT')
            ) {
                $treatment->setActive(false);
                $this->addFlash('success', $this->getFlashbagMessage('success', 'archive', $treatment));
            }
        }
        $this->entityManager->flush();

        return $this->redirectToRoute($this->getRouteName('list'));
    }

    /**
     * The delete action view
     * Display a confirmation message to confirm data deletion.
     */
    public function deleteAllAction(): Response
    {
        $request = $this->requestStack->getMasterRequest();
        $ids     = $request->query->get('ids');
        $ids     = explode(',', $ids);

        if (!$this->authorizationChecker->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer ces traitements');

            return $this->redirectToRoute($this->getRouteName('list'));
        }

        return $this->render($this->getTemplatingBasePath('delete_all'), [ // delete_all
            'ids'              => $ids,
            'treatment_length' => count($ids),
        ]);
    }

    public function deleteConfirmationAllAction(): Response
    {
        $request = $this->requestStack->getMasterRequest();
        $ids     = $request->query->get('ids');

        if (!$this->authorizationChecker->isGranted('ROLE_USER')) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer ces traitements');

            return $this->redirectToRoute($this->getRouteName('list'));
        }

        foreach ($ids as $id) {
            /**
             * @var Treatment
             */
            $treatment = $this->repository->findOneById($id);
            $user      = $this->getUser();
            if ($treatment
                && (
                    $user instanceof UserModel\User
                    && $treatment->getCollectivity() === $user->getCollectivity()
                    && (0 === count($user->getServices()) || in_array($treatment->getService(), $user->getServices()->toArray()))
                )
                || $this->authorizationChecker->isGranted('ROLE_REFERENT')
            ) {
                $this->deleteConfirmationAction($id);
            }
        }

        return $this->redirectToRoute($this->getRouteName('list'));
    }

    /**
     * {@inheritdoc}
     */
    protected function getLabelAndKeysArray(): array
    {
        if ($this->authorizationChecker->isGranted('ROLE_REFERENT')) {
            return [
                '1'  => 'name',
                '2'  => 'collectivite',
                '3'  => 'baseLegal',
                '4'  => 'logiciel',
                '5'  => 'enTantQue',
                '6'  => 'gestionnaire',
                '7'  => 'sousTraitant',
                '8'  => 'controleAcces',
                '9'  => 'tracabilite',
                '10' => 'saving',
                '11' => 'other',
                '12' => 'entitledPersons',
                '13' => 'openAccounts',
                '14' => 'specificitiesDelivered',
                '15' => 'updatedAt',
                '16' => 'public',
                '17' => 'update',
                '18' => 'responsableTraitement',
                '19' => 'specific_traitement',
                '20' => 'conformite_traitement',
                '21' => 'actions',
            ];
        }

        return [
            '1'  => 'name',
            '2'  => 'baseLegal',
            '3'  => 'logiciel',
            '4'  => 'enTantQue',
            '5'  => 'gestionnaire',
            '6'  => 'sousTraitant',
            '7'  => 'controleAcces',
            '8'  => 'tracabilite',
            '9'  => 'saving',
            '10' => 'other',
            '11' => 'entitledPersons',
            '12' => 'openAccounts',
            '13' => 'specificitiesDelivered',
            '14' => 'updatedAt',
            '15' => 'public',
            '16' => 'update',
            '17' => 'responsableTraitement',
            '18' => 'specific_traitement',
            '19' => 'conformite_traitement',
            '20' => 'actions',
        ];
    }
}
