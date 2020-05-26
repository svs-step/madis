<?php

namespace App\Domain\Registry\Controller;

use App\Application\Controller\CRUDController;
use App\Application\Symfony\Security\UserProvider;
use App\Domain\Registry\Form\Type\ConformiteOrganisation\EvaluationType;
use App\Domain\Registry\Model\ConformiteOrganisation\Conformite;
use App\Domain\Registry\Model\ConformiteOrganisation\Evaluation;
use App\Domain\Registry\Model\ConformiteOrganisation\Reponse;
use App\Domain\Registry\Repository\ConformiteOrganisation as Repository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConformiteOrganisationController extends CRUDController
{
    /**
     * @var Repository\Question
     */
    private $questionRepository;

    /**
     * @var Repository\Processus
     */
    private $processusRepository;

    /**
     * @var Repository\Conformite
     */
    private $conformiteRepository;

    /**
     * @var UserProvider
     */
    private $userProvider;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\Evaluation $repository,
        Repository\Question $questionRepository,
        Repository\Processus $processusRepository,
        Repository\Conformite $conformiteRepository,
        UserProvider $userProvider)
    {
        parent::__construct($entityManager, $translator, $repository);
        $this->questionRepository     = $questionRepository;
        $this->processusRepository    = $processusRepository;
        $this->conformiteRepository   = $conformiteRepository;
        $this->userProvider           = $userProvider;
    }

    public function createAction(Request $request): Response
    {
        $evaluation     = new Evaluation();

        $organisation = $this->userProvider->getAuthenticatedUser()->getCollectivity();
        $evaluation->setOrganisation($organisation);

        foreach ($this->processusRepository->findAll() as $processus) {
            $conformite = new Conformite();
            $conformite->setProcessus($processus);
            foreach ($this->questionRepository->findAllByProcessus($processus) as $question) {
                $reponse = new Reponse();
                $reponse->setConformite($conformite);
                $reponse->setQuestion($question);
                $conformite->addReponse($reponse);
            }
            $evaluation->addConformite($conformite);
        }

        $form = $this->createForm($this->getFormType(), $evaluation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($evaluation);
            $em->flush();

            $this->addFlash('success', $this->getFlashbagMessage('success', 'create', $evaluation));

            return $this->redirectToRoute($this->getRouteName('list'));
        }

        return $this->render($this->getTemplatingBasePath('create'), [
            'form'      => $form->createView(),
        ]);
    }

    protected function getDomain(): string
    {
        return 'registry';
    }

    protected function getModel(): string
    {
        return 'conformite_organisation';
    }

    protected function getModelClass(): string
    {
        return Evaluation::class;
    }

    protected function getFormType(): string
    {
        return EvaluationType::class;
    }

    protected function getListData()
    {
        return $this->repository->findAll();
    }
}
