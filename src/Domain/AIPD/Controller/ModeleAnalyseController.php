<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Controller;

use App\Application\Controller\CRUDController;
use App\Application\Symfony\Security\UserProvider;
use App\Application\Traits\ServersideDatatablesTrait;
use App\Domain\AIPD\Dictionary\BaseCriterePrincipeFondamental;
use App\Domain\AIPD\Form\Flow\ModeleAIPDFlow;
use App\Domain\AIPD\Form\Type\ImportModeleType;
use App\Domain\AIPD\Form\Type\ModeleAnalyseRightsType;
use App\Domain\AIPD\Form\Type\ModeleAnalyseType;
use App\Domain\AIPD\Model\ModeleAnalyse;
use App\Domain\AIPD\Model\ModeleQuestionConformite;
use App\Domain\AIPD\Repository;
use App\Domain\Registry\Repository\ConformiteTraitement\Question;
use App\Domain\User\Repository\Collectivity;
use Doctrine\ORM\EntityManagerInterface;
use Gaufrette\Exception\FileNotFound;
use Gaufrette\FilesystemInterface;
use JMS\Serializer\SerializerBuilder;
use Knp\Snappy\Pdf;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Exception\NotImplementedException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property Repository\ModeleAnalyse $repository
 */
class ModeleAnalyseController extends CRUDController
{
    use ServersideDatatablesTrait;

    /**
     * @var Collectivity
     */
    protected $collectivityRepository;

    private ModeleAIPDFlow $modeleFlow;
    private Question $questionRepository;
    private RouterInterface $router;
    private FilesystemInterface $fichierFilesystem;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\ModeleAnalyse $repository,
        Pdf $pdf,
        UserProvider $userProvider,
        AuthorizationCheckerInterface $authorizationChecker,
        Collectivity $collectivityRepository,
        ModeleAIPDFlow $modeleFlow,
        Question $questionRepository,
        RouterInterface $router,
        FilesystemInterface $fichierFilesystem
    ) {
        parent::__construct($entityManager, $translator, $repository, $pdf, $userProvider, $authorizationChecker);
        $this->collectivityRepository   = $collectivityRepository;
        $this->modeleFlow               = $modeleFlow;
        $this->questionRepository       = $questionRepository;
        $this->router                   = $router;
        $this->fichierFilesystem        = $fichierFilesystem;
    }

    protected function getDomain(): string
    {
        return 'aipd';
    }

    protected function getModel(): string
    {
        return 'modele_analyse';
    }

    protected function getModelClass(): string
    {
        return ModeleAnalyse::class;
    }

    protected function getFormType(): string
    {
        return ModeleAnalyseType::class;
    }

    public function listAction(): Response
    {
        return $this->render('Aipd/Modele_analyse/list.html.twig', [
            'totalItem' => $this->repository->count(),
            'route'     => $this->router->generate('aipd_modele_analyse_list_datatables'),
        ]);
    }

    /**
     * {@inheritdoc}
     * - Upload documentFile before object persistence in database.
     *
     * @throws \Exception
     */
    public function formPrePersistData($object)
    {
        if (!$object instanceof ModeleAnalyse) {
            throw new \RuntimeException('You must persist a ' . ModeleAnalyse::class . ' object class with your form');
        }

        foreach ($object->getCriterePrincipeFondamentaux() as $criterePrincipeFondamental) {
            $deleteFile = $criterePrincipeFondamental->isDeleteFile();

            if ($deleteFile) {
                //Remove existing file
                try {
                    $this->fichierFilesystem->delete($criterePrincipeFondamental->getFichier());
                } catch (FileNotFound $e) {
                }

                $criterePrincipeFondamental->setFichier(null);
            }

            $file = $criterePrincipeFondamental->getFichierFile();

            if ($file) {
                if (null !== $existing = $criterePrincipeFondamental->getFichier()) {
                    try {
                        $this->fichierFilesystem->delete($existing);
                    } catch (FileNotFound $e) {
                    }
                }
                $filename = Uuid::uuid4()->toString() . '.' . $file->getClientOriginalExtension();
                $this->fichierFilesystem->write($filename, \fopen($file->getRealPath(), 'r'));
                $criterePrincipeFondamental->setFichier($filename);
                $criterePrincipeFondamental->setFichierFile(null);
            }
        }
    }

    public function createAction(Request $request): Response
    {
        $object = new ModeleAnalyse();
        $object->setCriterePrincipeFondamentaux(BaseCriterePrincipeFondamental::getBaseCritere());
        $object->setQuestionConformites($this->getQuestionsConformite($object));

        $this->modeleFlow->bind($object);
        $form = $this->modeleFlow->createForm();

        if ($this->modeleFlow->isValid($form)) {
            $this->formPrePersistData($object);
            $this->modeleFlow->saveCurrentStepData($form);

            if ($this->modeleFlow->nextStep()) {
                $form = $this->modeleFlow->createForm();
            } else {
                $this->entityManager->persist($object);
                $this->entityManager->flush();

                $this->modeleFlow->reset();

                return $this->redirectToRoute($this->getRouteName('list'));
            }
        }

        return $this->render($this->getTemplatingBasePath('create'), [
            'form' => $form->createView(),
            'flow' => $this->modeleFlow,
        ]);
    }

    public function editAction(Request $request, string $id): Response
    {
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        $this->modeleFlow->bind($object);
        $form = $this->modeleFlow->createForm();

        if ($this->modeleFlow->isValid($form)) {
            $this->formPrePersistData($object);
            $this->modeleFlow->saveCurrentStepData($form);

            if ($this->modeleFlow->nextStep()) {
                $form = $this->modeleFlow->createForm();
            } else {
                $this->entityManager->persist($object);
                $this->entityManager->flush();

                $this->modeleFlow->reset();

                return $this->redirectToRoute($this->getRouteName('list'));
            }
        }

        return $this->render($this->getTemplatingBasePath('edit'), [
            'form' => $form->createView(),
            'flow' => $this->modeleFlow,
        ]);
    }

    private function getQuestionsConformite(ModeleAnalyse $modeleAnalyse)
    {
        $questions = [];
        foreach ($this->questionRepository->findAll(['position' => 'ASC']) as $question) {
            $questions[] = new ModeleQuestionConformite($question->getQuestion(), $question->getPosition(), $modeleAnalyse);
        }

        return $questions;
    }

    public function duplicateAction(string $id): Response
    {
        throw new NotImplementedException('Not implemented yet');
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
            'form'  => $form->createView(),
        ]);
    }

    public function listDataTables(Request $request): JsonResponse
    {
        $modeles = $this->getResults($request);
        $reponse = $this->getBaseDataTablesResponse($request, $modeles);

        foreach ($modeles as $modele) {
            if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                $userCollectivity               = $this->userProvider->getAuthenticatedUser()->getCollectivity();
                $userCollectivityType           = $userCollectivity->getType();
                $authorizedCollectivities       = $modele->getAuthorizedCollectivities();
                $authorizedCollectivityTypes    = $modele->getAuthorizedCollectivityTypes();

                if (!\is_null($authorizedCollectivityTypes)
                && in_array($userCollectivityType, $authorizedCollectivityTypes)) {
                    continue;
                }

                if ($authorizedCollectivities->contains($userCollectivity)) {
                    continue;
                }
            }

            $reponse['data'][] = [
                'nom'         => $modele->getNom(),
                'description' => $modele->getDescription(),
                'updatedAt'   => date_format($modele->getUpdatedAt(), 'd-m-Y'),
                'actions'     => $this->generateActioNCellContent($modele),
            ];
        }

        $jsonResponse = new JsonResponse($reponse);

        return $jsonResponse;
    }

    private function generateActioNCellContent(ModeleAnalyse $modele)
    {
        $id                     = $modele->getId();
        $htmltoReturnIfAdmin    = '';

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $htmltoReturnIfAdmin =
            '<a href="' . $this->router->generate('aipd_modele_analyse_rights', ['id' => $id]) . '">
                <i class="fa fa-user-shield"></i>'
                . $this->translator->trans('action.rights') .
            '</a>';
        }

        return
            '<a href="' . $this->router->generate('aipd_modele_analyse_edit', ['id' => $id]) . '">
                <i class="fa fa-pencil-alt"></i>'
                . $this->translator->trans('action.edit') .
            '</a>'
            . $htmltoReturnIfAdmin .
            '<a href="' . $this->router->generate('aipd_modele_analyse_export', ['id' => $id]) . '">
                <i class="fa fa-file-code"></i>' .
                $this->translator->trans('action.export') .
            '</a>' .
            '<a href="' . $this->router->generate('aipd_modele_analyse_delete', ['id' => $id]) . '">
                <i class="fa fa-trash"></i>' .
                $this->translator->trans('action.delete') .
            '</a>';
    }

    protected function getLabelAndKeysArray(): array
    {
        return [
            '0' => 'nom',
            '1' => 'description',
            '2' => 'updatedAt',
            '3' => 'actions',
        ];
    }

    public function exportAction(string $id)
    {
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }
        /** @var ModeleAnalyse $toExport */
        $toExport = clone $object;
        $toExport->setCriterePrincipeFondamentaux($toExport->getCriterePrincipeFondamentaux()->toArray());

        $serializer = SerializerBuilder::create()->build();
        $xml        = $serializer->serialize($toExport, 'xml');

        $response    = new Response($xml);
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            self::formatToFileCompliant($object->getNom()) . '.xml'
        );

        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    public function importAction(Request $request)
    {
        $form = $this->createForm(ImportModeleType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $content    = file_get_contents($form->getData()['file']->getPathname());
            $serializer = SerializerBuilder::create()->build();
            $object     = $serializer->deserialize($content, ModeleAnalyse::class, 'xml');
            $object->deserialize();
            $object->setNom('(import) ' . $object->getNom());
            $this->entityManager->persist($object);
            $this->entityManager->flush();
            $this->addFlash('success', $this->getFlashbagMessage('success', 'import', $object));

            return $this->redirectToRoute($this->getRouteName('list'));
        }

        return $this->render($this->getTemplatingBasePath('import'), [
            'form' => $form->createView(),
        ]);
    }

    private static function formatToFileCompliant(string $string)
    {
        $unwanted_array = [
            'Š'=> 'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
            'Ê'=> 'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
            'Ú'=> 'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
            'è'=> 'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
            'ö'=> 'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y',
        ];

        return strtr($string, $unwanted_array);
    }
}
