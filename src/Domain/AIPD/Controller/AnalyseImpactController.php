<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Controller;

use App\Application\Controller\CRUDController;
use App\Application\Symfony\Security\UserProvider;
use App\Application\Traits\ServersideDatatablesTrait;
use App\Domain\AIPD\Dictionary\ReponseAvisDictionary;
use App\Domain\AIPD\Form\Flow\AnalyseImpactFlow;
use App\Domain\AIPD\Form\Type\AnalyseAvisType;
use App\Domain\AIPD\Form\Type\AnalyseImpactType;
use App\Domain\AIPD\Model\AnalyseAvis;
use App\Domain\AIPD\Model\AnalyseImpact;
use App\Domain\AIPD\Model\AnalyseScenarioMenace;
use App\Domain\AIPD\Model\CriterePrincipeFondamental;
use App\Domain\AIPD\Repository;
use App\Domain\User\Model\Collectivity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Gaufrette\Filesystem;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property Repository\AnalyseImpact $repository
 */
class AnalyseImpactController extends CRUDController
{
    use ServersideDatatablesTrait;

    private RouterInterface $router;
    private RequestStack $requestStack;
    private $modeleRepository;
    private AnalyseImpactFlow $analyseFlow;
    private Filesystem $fichierFilesystem;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\AnalyseImpact $repository,
        Pdf $pdf,
        UserProvider $userProvider,
        AuthorizationCheckerInterface $authorizationChecker,
        RouterInterface $router,
        RequestStack $requestStack,
        Repository\ModeleAnalyse $modeleRepository,
        AnalyseImpactFlow $analyseFlow,
        Filesystem $fichierFilesystem
    ) {
        parent::__construct($entityManager, $translator, $repository, $pdf, $userProvider, $authorizationChecker);
        $this->router            = $router;
        $this->requestStack      = $requestStack;
        $this->modeleRepository  = $modeleRepository;
        $this->analyseFlow       = $analyseFlow;
        $this->fichierFilesystem = $fichierFilesystem;
    }

    protected function getDomain(): string
    {
        return 'aipd';
    }

    protected function getModel(): string
    {
        return 'analyse_impact';
    }

    protected function getModelClass(): string
    {
        return AnalyseImpact::class;
    }

    protected function getFormType(): string
    {
        return AnalyseImpactType::class;
    }

    public function listAction(): Response
    {
        return $this->render($this->getTemplatingBasePath('list'), [
            'totalItem' => $this->repository->count(),
            'route'     => $this->router->generate('aipd_analyse_impact_datatables'),
        ]);
    }

    public function listDataTables(Request $request): JsonResponse
    {
        $request  = $this->requestStack->getMasterRequest();
        $user     = $this->userProvider->getAuthenticatedUser();
        $criteria = [];

        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $criteria['collectivity'] = $user->getCollectivity();
        }

        $analyses = $this->getResults($request, $criteria);
        $response = $this->getBaseDataTablesResponse($request, $analyses);

        /** @var AnalyseImpact $analyse */
        foreach ($analyses as $analyse) {
            $response['data'][] = [
                'traitement'       => '<a href="' . $this->router->generate('registry_treatment_show', ['id' => $analyse->getConformiteTraitement()->getTraitement()->getId()]) . '">' . $analyse->getConformiteTraitement()->getTraitement()->getName() . '</a>',
                'dateDeCreation'   => $analyse->getCreatedAt()->format('d/m/Y'),
                'dateDeValidation' => null === $analyse->getDateValidation() ? '' : $analyse->getDateValidation()->format('d/m/Y'),
                'modele'           => $analyse->getModeleAnalyse(),
                'collectivite'     => $analyse->getConformiteTraitement()->getTraitement()->getCollectivity()->getShortName(),
                'avisReferent'     => $this->generateAvisLabel($analyse->getAvisReferent()),
                'avisDpd'          => $this->generateAvisLabel($analyse->getAvisDpd()),
                'avisRepresentant' => $this->generateAvisLabel($analyse->getAvisRepresentant()),
                'avisResponsable'  => $this->generateAvisLabel($analyse->getAvisResponsable()),
                'actions'          => $this->generateActionCell($analyse),
            ];
        }

        return new JsonResponse($response);
    }

    protected function getLabelAndKeysArray(): array
    {
        return [
            0 => 'traitement',
            1 => 'dateDeCreation',
            2 => 'dateDeValidation',
            3 => 'modele',
            4 => 'collectivite',
            5 => 'avisReferent',
            6 => 'avisDpd',
            7 => 'avisRepresentant',
            8 => 'avisResponsable',
            9 => 'actions',
        ];
    }

    private function generateActionCell(AnalyseImpact $analyseImpact): string
    {
        $cell = '<a href="' . $this->router->generate('aipd_analyse_impact_print', ['id' => $analyseImpact->getId()]) . '">
        <i class="fa fa-print"></i>' .
            $this->translator->trans('action.print') . '
        </a>';

        $user = $this->userProvider->getAuthenticatedUser();
        if ($user->getRoles()[0] !=='ROLE_PREVIEW') {
            if (!$analyseImpact->isValidated()) {
                $cell .= '<a href="' . $this->router->generate('aipd_analyse_impact_edit', ['id' => $analyseImpact->getId()]) . '">
                    <i class="fa fa-pencil-alt"></i>' .
                    $this->translator->trans('action.edit') . '
                    </a>';
                if ($analyseImpact->isReadyForValidation()) {
                    $cell .= '<a href="' . $this->router->generate('aipd_analyse_impact_validation', ['id' => $analyseImpact->getId()]) . '">
                    <i class="fa fa-check-square"></i>' .
                        $this->translator->trans('action.validate') . '
                    </a>';
                }
            }
            $cell .= '                                    <a href="' . $this->router->generate('aipd_analyse_impact_delete', ['id' => $analyseImpact->getId()]) . '">
            <i class="fa fa-trash"></i> ' .
                $this->translator->trans('action.delete') . '
            </a>';
        }

        return $cell;
    }

    public function generateAvisLabel(AnalyseAvis $avis)
    {
        switch ($avis->getReponse()) {
            case ReponseAvisDictionary::REPONSE_FAVORABLE:
                $color = 'success';
                break;
            case ReponseAvisDictionary::REPONSE_FAVORABLE_RESERVE:
                $color = 'warning';
                break;
            case ReponseAvisDictionary::REPONSE_DEFAVORABLE:
                $color = 'danger';
                break;
            default:
                $color = 'default';
        }

        return '<span class="label label-' . $color . '" style="min-width: 100%; display: inline-block;">' . ReponseAvisDictionary::getReponseAvis()[$avis->getReponse()] . '</span>';
    }

    /**
     * {@inheritdoc}
     * - Upload documentFile before object persistence in database.
     *
     * @throws \Exception
     */
    public function formPrePersistData($object)
    {
        if (!$object instanceof AnalyseImpact) {
            throw new \RuntimeException('You must persist a ' . AnalyseImpact::class . ' object class with your form');
        }

        foreach ($object->getCriterePrincipeFondamentaux() as $criterePrincipeFondamental) {
            $file = $criterePrincipeFondamental->getFichierFile();

            if ($file) {
                $filename = Uuid::uuid4()->toString() . '.' . $file->getClientOriginalExtension();
                $this->fichierFilesystem->write($filename, \fopen($file->getRealPath(), 'r'));
                $criterePrincipeFondamental->setFichier($filename);
                $criterePrincipeFondamental->setFichierFile(null);
            }
        }
    }

    /**
     * The creation action view
     * Create a new data.
     */
    public function createAnalyseAction(Request $request, string $id): Response
    {
        if (null === $object = $this->repository->findOneByIdWithoutInvisibleScenarios($id)) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        $this->analyseFlow->bind($object);
        $form = $this->analyseFlow->createForm();

        if ($this->analyseFlow->isValid($form)) {
            $this->formPrePersistData($object);
            $this->analyseFlow->saveCurrentStepData($form);

            if ($this->analyseFlow->nextStep()) {
                $form = $this->analyseFlow->createForm();
            // TODO Persist and flush here to allow draft ?
            } else {
                $this->entityManager->persist($object);
                $this->entityManager->flush();

                $this->analyseFlow->reset();

                $this->addFlash('success', $this->getFlashbagMessage('success', 'create', $object));

                return $this->redirectToRoute($this->getRouteName('evaluation'), [
                    'id' => $id,
                ]);
            }
        }

        return $this->render($this->getTemplatingBasePath('create'), [
            'flow' => $this->analyseFlow,
            'form' => $form->createView(),
        ]);
    }

    public function editAction(Request $request, string $id): Response
    {
        if (null === $object = $this->repository->findOneByIdWithoutInvisibleScenarios($id)) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }
        /**
         * @var AnalyseImpact $object
         */
        if ($object->isValidated()) {
            $this->addFlash('info', $this->getFlashbagMessage('info', 'cant_edit', $object));

            return $this->redirectToRoute($this->getRouteName('list'));
        }

        $this->analyseFlow->bind($object);
        $form = $this->analyseFlow->createForm();

        if ($this->analyseFlow->isValid($form)) {
            $this->formPrePersistData($object);
            $this->analyseFlow->saveCurrentStepData($form);

            if ($this->analyseFlow->nextStep()) {
                $form = $this->analyseFlow->createForm();
            } else {
                $this->entityManager->persist($object);
                $this->entityManager->flush();

                $this->analyseFlow->reset();

                $this->addFlash('success', $this->getFlashbagMessage('success', 'edit', $object));

                return $this->redirectToRoute($this->getRouteName('evaluation'), [
                    'id' => $id,
                ]);
            }
        }

        return $this->render($this->getTemplatingBasePath('edit'), [
            'flow' => $this->analyseFlow,
            'form' => $form->createView(),
        ]);
    }

    public function modelesDatatablesAction()
    {
        $request      = $this->requestStack->getMasterRequest();
        $collectivity = $this->entityManager->getRepository(Collectivity::class)->find($request->query->get('collectivity'));

        $modeles = $this->getModeleResults($request);

        $reponse = $this->getBaseDataTablesResponse($request, $modeles);
        foreach ($modeles as $modele) {
            $collectivityType            = $collectivity->getType();
            $authorizedCollectivities    = $modele->getAuthorizedCollectivities();
            $authorizedCollectivityTypes = $modele->getAuthorizedCollectivityTypes();

            if ((!\is_null($authorizedCollectivityTypes)
                && in_array($collectivityType, $authorizedCollectivityTypes)) ||
                $authorizedCollectivities->contains($collectivity)
            ) {
                $reponse['data'][] = [
                    'nom'         => '<input type="radio" value="' . $modele->getId() . '" name="modele_choice" required="true"/> ' . $modele->getNom(),
                    'description' => $modele->getDescription(),
                ];
            }
        }
        $reponse['recordsTotal']    = count($reponse['data']);
        $reponse['recordsFiltered'] = count($reponse['data']);

        return new JsonResponse($reponse);
    }

    public function evaluationAction(string $id)
    {
        if (null === $object = $this->repository->findOneById($id)) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        return $this->render($this->getTemplatingBasePath('evaluation'), [
            'analyseImpact' => $object,
        ]);
    }

    protected function getModeleResults(Request $request): ?Paginator
    {
        $first      = $request->request->get('start');
        $maxResults = $request->request->get('length');
        $orders     = $request->request->get('order');
        $columns    = $request->request->get('columns');

        $orderColumn = $this->getCorrespondingLabelFromkeyForModeles($orders[0]['column']);
        $orderDir    = $orders[0]['dir'];

        $searches = [];
        foreach ($columns as $column) {
            if ('' !== $column['search']['value']) {
                $searches[$column['data']] = $column['search']['value'];
            }
        }

        return $this->modeleRepository->findPaginated($first, $maxResults, $orderColumn, $orderDir, $searches);
    }

    private function getCorrespondingLabelFromkeyForModeles(string $key)
    {
        return \array_key_exists($key, $this->getLabelAndKeysArrayForModeles()) ? $this->getLabelAndKeysArrayForModeles()[$key] : null;
    }

    private function getLabelAndKeysArrayForModeles()
    {
        return [
            '0' => 'nom',
            '1' => 'description',
        ];
    }

    public function printAction(Request $request, string $id)
    {
        if (null === $object = $this->repository->findOneById($id)) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        $this->pdf->setOption('header-html', $this->renderView($this->getTemplatingBasePath('pdf_header')));
        $this->pdf->setOption('margin-top', '20');
        $this->pdf->setOption('margin-bottom', '15');
        $this->pdf->setOption('margin-left', '20');
        $this->pdf->setOption('margin-right', '20');

        $slugger  = new AsciiSlugger();
        $filename = $slugger->slug($object->getConformiteTraitement()->getTraitement()->getName());

        $mesures   = [];
        $scenarios = $object->getScenarioMenaces();

        foreach ($scenarios as $scenario) {
            /*
             * @var AnalyseScenarioMenace
             */
            if ('negligeable' !== $scenario->getGravite() || 'vide' !== $scenario->getGravite() || 'negligeable' !== $scenario->getVraisemblance() || 'vide' !== $scenario->getVraisemblance()) {
                foreach ($scenario->getMesuresProtections() as $mesure) {
                    if (!array_key_exists($mesure->getNom(), $mesures) && ($mesure->getPoidsGravite() <= 1 || $mesure->getPoidsVraisemblance() <= 1)) {
                        $mesures[$mesure->getNom()] = $mesure;
                    }
                }
            }
        }

        return new PdfResponse(
            $this->pdf->getOutputFromHtml(
                $this->renderView($this->getTemplatingBasePath('pdf'), [
                    'object'            => $object,
                    'mesuresProtection' => $mesures,
                    'base_dir'          => $this->getParameter('kernel.project_dir') . '/public' . $request->getBasePath(),
                ]), ['javascript-delay' => 1000]),
            $filename . '.pdf'
        );
    }

    public function validationAction(Request $request, string $id)
    {
        if (null === $object = $this->repository->findOneById($id)) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }
        if (!$object->isReadyForValidation()) {
            $object->setIsReadyForValidation(true);
            $this->entityManager->flush();
        }
        /** @var Form $form */
        $form = $this->createForm(AnalyseAvisType::class, $object);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ('saveDraft' !== $form->getClickedButton() && ReponseAvisDictionary::REPONSE_NONE !== $object->getAvisResponsable()->getReponse()) {
                $object->setDateValidation(new \DateTime());
                $object->setIsValidated(true);
                $object->setStatut($object->getAvisResponsable()->getReponse());
            }
            $this->entityManager->flush();

            return $this->redirectToRoute($this->getRouteName('list'));
        }

        return $this->render($this->getTemplatingBasePath('validation'), [
            'form' => $form->createView(),
        ]);
    }

    public function apiDeleteFile(ManagerRegistry $doctrine, Request $request): Response
    {
        $id                  = $request->get('id');
        $this->entityManager = $doctrine->getManager();
        $critere             = $doctrine->getRepository(CriterePrincipeFondamental::class)
            ->findOneBy(['fichier' => $id]);

        $critere->setFichier(null);
        $this->entityManager->persist($critere);
        $this->entityManager->flush();

        $jsonResponse = new JsonResponse();
        $jsonResponse->setJson(json_encode($critere));

        return $jsonResponse;
    }
}
