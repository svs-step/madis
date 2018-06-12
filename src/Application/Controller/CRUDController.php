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

namespace App\Application\Controller;

use App\Application\DDD\Repository\RepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\TranslatorInterface;

abstract class CRUDController extends Controller
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        RepositoryInterface $repository
    ) {
        $this->entityManager = $entityManager;
        $this->translator    = $translator;
        $this->repository    = $repository;
    }

    /**
     * Get the domain of the object.
     * (Formatted as string word).
     *
     * @return string
     */
    abstract protected function getDomain(): string;

    /**
     * Get the model of the object.
     * (Formatted as string word).
     *
     * @return string
     */
    abstract protected function getModel(): string;

    /**
     * Get the model class name of the object.
     * This methods return the class name with it namespace.
     *
     * @return string
     */
    abstract protected function getModelClass(): string;

    /**
     * Get the form type class name to use during create & edit action.
     *
     * @return string
     */
    abstract protected function getFormType(): string;

    /**
     * Generate the templating base path dynamically depending on the domain, model & template.
     *
     * @param string|null $template The template to display
     *
     * @return string The generated templating base path
     */
    protected function getTemplatingBasePath(string $template = null): string
    {
        // TODO: Check template existence
        $domain = \ucfirst($this->getDomain());
        $model  = \ucfirst($this->getModel());

        return "{$domain}/{$model}/{$template}.html.twig";
    }

    /**
     * Generate the flashbag message dynamically depending on the domain, model & object.
     *
     * @param string      $type     The flashbag type
     * @param string|null $template The related template to use
     * @param null|mixed  $object   The object to use to generate flashbag (eg. show object name)
     *
     * @return string The generated flashbag
     */
    protected function getFlashbagMessage(string $type, string $template = null, $object = null): string
    {
        $params = [];
        if (!\is_null($object)) {
            $params['%object%'] = $object;
        }

        return $this->translator->trans(
            "{$this->getDomain()}.{$this->getModel()}.flashbag.{$type}.{$template}",
            $params
        );
    }

    /**
     * Generate route name depending on the template.
     *
     * @param string|null $template The template to use for generation
     *
     * @return string The generated route name
     */
    protected function getRouteName(string $template = null): string
    {
        return "{$this->getDomain()}_{$this->getModel()}_{$template}";
    }

    /**
     * Get data to use in List view.
     *
     * @return array
     */
    protected function getListData()
    {
        return $this->repository->findAll();
    }

    /**
     * The list action view
     * Get data & display them.
     *
     * @return Response
     */
    public function listAction(): Response
    {
        return $this->render($this->getTemplatingBasePath('list'), [
            'objects' => $this->getListData(),
        ]);
    }

    /**
     * The creation action view
     * Create a new data.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        $modelClass = $this->getModelClass();
        $object     = new $modelClass();
        $form       = $this->createForm($this->getFormType(), $object);

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

    /**
     * The edition action view
     * Edit an existing data.
     *
     * @param Request $request
     * @param string  $id      The ID of the data to edit
     *
     * @return Response
     */
    public function editAction(Request $request, string $id): Response
    {
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }
        $form = $this->createForm($this->getFormType(), $object);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($object);
            $this->entityManager->flush();

            $this->addFlash('success', $this->getFlashbagMessage('success', 'edit', $object));

            return $this->redirectToRoute($this->getRouteName('list'));
        }

        return $this->render($this->getTemplatingBasePath('edit'), [
            'form' => $form->createView(),
        ]);
    }

    /**
     * The show action view
     * Display the object information.
     *
     * @param string $id The ID of the data to display
     *
     * @return Response
     */
    public function showAction(string $id): Response
    {
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        return $this->render($this->getTemplatingBasePath('show'), [
            'object' => $object,
        ]);
    }

    /**
     * The delete action view
     * Display a confirmation message to confirm data deletion.
     *
     * @param string $id
     *
     * @return Response
     */
    public function deleteAction(string $id): Response
    {
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        return $this->render($this->getTemplatingBasePath('delete'), [
            'object' => $object,
        ]);
    }

    /**
     * The deletion action
     * Delete the data.
     *
     * @param string $id
     *
     * @return Response
     */
    public function deleteConfirmationAction(string $id): Response
    {
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        $this->entityManager->remove($object);
        $this->entityManager->flush();

        $this->addFlash('success', $this->getFlashbagMessage('success', 'delete', $object));

        return $this->redirectToRoute($this->getRouteName('list'));
    }
}
