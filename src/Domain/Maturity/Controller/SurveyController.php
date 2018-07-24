<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan.bourlard@outlook.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\Maturity\Controller;

use App\Application\Controller\CRUDController;
use App\Domain\Maturity\Form\Type\SurveyType;
use App\Domain\Maturity\Model;
use App\Domain\Maturity\Repository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class SurveyController extends CRUDController
{
    /**
     * @var Repository\Question
     */
    private $questionRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\Survey $repository,
        Repository\Question $questionRepository
    ) {
        parent::__construct($entityManager, $translator, $repository);
        $this->questionRepository = $questionRepository;
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
     * Override method in order to hydrate survey answers.
     */
    public function createAction(Request $request): Response
    {
        /**
         * @var Model\Survey
         */
        $modelClass = $this->getModelClass();
        $object     = new $modelClass();

        // Before create form, hydrate answers array with potential question responses
        foreach ($this->questionRepository->findAll() as $question) {
            $answer = new Model\Answer();
            $answer->setQuestion($question);
            $object->addAnswer($answer);
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

        return $this->render($this->getTemplatingBasePath('create'), [
            'form' => $form->createView(),
        ]);
    }
}
