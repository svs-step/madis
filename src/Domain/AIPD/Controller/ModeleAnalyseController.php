<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Controller;

use App\Application\Controller\CRUDController;
use App\Application\Symfony\Security\UserProvider;
use App\Application\Traits\ServersideDatatablesTrait;
use App\Domain\AIPD\Dictionary\BaseCriterePrincipeFondamental;
use App\Domain\AIPD\Form\Flow\ModeleAIPDFlow;
use App\Domain\AIPD\Form\Type\ModeleAnalyseType;
use App\Domain\AIPD\Model\ModeleAnalyse;
use App\Domain\AIPD\Model\ModeleQuestionConformite;
use App\Domain\AIPD\Repository;
use App\Domain\Registry\Repository\ConformiteTraitement\Question;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    private ModeleAIPDFlow $modeleFlow;
    private Question $questionRepository;
    private RouterInterface $router;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\ModeleAnalyse $repository,
        Pdf $pdf,
        UserProvider $userProvider,
        AuthorizationCheckerInterface $authorizationChecker,
        ModeleAIPDFlow $modeleFlow,
        Question $questionRepository,
        RouterInterface $router
    ) {
        parent::__construct($entityManager, $translator, $repository, $pdf, $userProvider, $authorizationChecker);
        $this->modeleFlow         = $modeleFlow;
        $this->questionRepository = $questionRepository;
        $this->router             = $router;
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

    public function createAction(Request $request): Response
    {
        $object = new ModeleAnalyse();
        $object->setCriterePrincipeFondamentaux(BaseCriterePrincipeFondamental::getBaseCritere());
        $object->setQuestionConformites($this->getQuestionsConformite($object));

        dump($object);
        $this->modeleFlow->bind($object);
        $form = $this->modeleFlow->createForm();

        if ($this->modeleFlow->isValid($form)) {
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
            $questions[] = new ModeleQuestionConformite($question->getQuestion(), $modeleAnalyse);
        }

        return $questions;
    }

    public function duplicateAction(string $id): Response
    {
        throw new NotImplementedException('Not implemented yet');
    }

    public function listDataTables(Request $request): JsonResponse
    {
        $modeles = $this->getResults($request);
        $reponse = $this->getBaseDataTablesResponse($request, $modeles);

        foreach ($modeles as $modele) {
            $reponse['data'][] = [
                'nom'         => $modele->getNom(),
                'description' => $modele->getDescription(),
                'updatedAt'   => date_format($modele->getUpdatedAt(), 'd-m-Y'),
                'actions'     => $this->generateActioNCellContent($modele),
            ];
        }

        $jsonResponse = new JsonResponse();
        $jsonResponse->setJson(json_encode($reponse));

        return $jsonResponse;
    }

    private function generateActioNCellContent(ModeleAnalyse $modele)
    {
        $id = $modele->getId();

        return
            '<a href="' . $this->router->generate('aipd_modele_analyse_edit', ['id' => $id]) . '">
                <i class="fa fa-pencil-alt"></i>'
                . $this->translator->trans('action.edit') .
            '</a>
            <a href="' . $this->router->generate('aipd_modele_analyse_duplicate', ['id' => $id]) . '">
                <i class="fa fa-clone"></i>'
                . $this->translator->trans('action.duplicate') .
            '</a>
            <a href="' . $this->router->generate('aipd_modele_analyse_delete', ['id' => $id]) . '">
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
}
