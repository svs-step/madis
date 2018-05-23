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

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator
    ) {
        $this->entityManager = $entityManager;
        $this->translator    = $translator;
    }

    abstract protected function getDomain(): string;

    abstract protected function getModel(): string;

    abstract protected function getModelClass(): string;

    abstract protected function getRepository(): ObjectRepository;

    abstract protected function getFormType(): string;

    protected function getTemplatingBasePath(string $template = null): string
    {
        $domain = \ucfirst($this->getDomain());
        $model  = \ucfirst($this->getModel());

        return "{$domain}/{$model}/{$template}.html.twig";
    }

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

    protected function getRouteName(string $template = null): string
    {
        return "{$this->getDomain()}_{$this->getModel()}_{$template}";
    }

    public function listAction(): Response
    {
        $objects = $this->getRepository()->findAll();

        return $this->render($this->getTemplatingBasePath('list'), [
            'objects' => $objects,
        ]);
    }

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

    public function editAction(Request $request, string $id): Response
    {
        $object = $this->getRepository()->find($id);
        $form   = $this->createForm($this->getFormType(), $object);

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

    public function deleteAction(string $id): Response
    {
        $object = $this->getRepository()->find($id);

        return $this->render($this->getTemplatingBasePath('delete'), [
            'object' => $object,
        ]);
    }

    public function deleteConfirmationAction(string $id): Response
    {
        $object = $this->getRepository()->find($id);
        $this->entityManager->remove($object);
        $this->entityManager->flush();

        $this->addFlash('success', $this->getFlashbagMessage('success', 'delete', $object));

        return $this->redirectToRoute($this->getRouteName('list'));
    }
}
