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

namespace App\Application\Controller;

use App\Application\DDD\Repository\RepositoryInterface;
use App\Application\Doctrine\Repository\CRUDRepository;
use App\Application\Interfaces\CollectivityRelated;
use App\Application\Symfony\Security\UserProvider;
use App\Domain\Notification\Model\Notification;
use App\Domain\Tools\ChainManipulator;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Polyfill\Intl\Icu\Exception\MethodNotImplementedException;

abstract class CRUDController extends AbstractController
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
     * @var CRUDRepository
     */
    protected $repository;

    /**
     * @var Pdf
     */
    protected $pdf;

    /**
     * @var UserProvider
     */
    protected $userProvider;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * CRUDController constructor.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        RepositoryInterface $repository,
        Pdf $pdf,
        UserProvider $userProvider,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->entityManager        = $entityManager;
        $this->translator           = $translator;
        $this->repository           = $repository;
        $this->pdf                  = $pdf;
        $this->userProvider         = $userProvider;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Get the domain of the object.
     * (Formatted as string word).
     */
    abstract protected function getDomain(): string;

    /**
     * Get the model of the object.
     * (Formatted as string word).
     */
    abstract protected function getModel(): string;

    /**
     * Get the model class name of the object.
     * This methods return the class name with it namespace.
     */
    abstract protected function getModelClass(): string;

    /**
     * Get the form type class name to use during create & edit action.
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
     * Replace word `%object%` in translation by the related object (thanks to it `__toString` method).
     *
     * @param string      $type     The flashbag type
     * @param string|null $template The related template to use
     * @param mixed|null  $object   The object to use to generate flashbag (eg. show object name)
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
     */
    public function listAction(): Response
    {
        return $this->render($this->getTemplatingBasePath('list'), [
            'objects' => $this->getListData(),
        ]);
    }

    /**
     * Actions to make when a form is submitted and valid.
     * This method is handled just after form validation, before object manipulation.
     *
     * @param mixed $object
     */
    public function formPrePersistData($object)
    {
    }

    /**
     * The creation action view
     * Create a new data.
     */
    public function createAction(Request $request): Response
    {
        $modelClass = $this->getModelClass();
        /** @var CollectivityRelated $object */
        $object         = new $modelClass();
        $serviceEnabled = false;

        if ($object instanceof CollectivityRelated) {
            $user = $this->userProvider->getAuthenticatedUser();
            $object->setCollectivity($user->getCollectivity());
            $serviceEnabled = $object->getCollectivity()->getIsServicesEnabled();
        }

        $form = $this->createForm($this->getFormType(), $object, ['validation_groups' => ['default', $this->getModel(), 'create']]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->formPrePersistData($object);
            $em = $this->getDoctrine()->getManager();

            $em->persist($object);
            $em->flush();

            $this->addFlash('success', $this->getFlashbagMessage('success', 'create', $object));

            return $this->redirectToRoute($this->getRouteName('list'));
        }

        return $this->render($this->getTemplatingBasePath('create'), [
            'form'           => $form->createView(),
            'object'         => $object,
            'serviceEnabled' => $serviceEnabled,
        ]);
    }

    /**
     * The edition action view
     * Edit an existing data.
     *
     * @param string $id The ID of the data to edit
     */
    public function editAction(Request $request, string $id): Response
    {
//        /** @var CollectivityRelated $object */
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        $serviceEnabled = false;

        if ($object instanceof Collectivity) {
            $serviceEnabled = $object->getIsServicesEnabled();
        } elseif ($object instanceof CollectivityRelated) {
            $serviceEnabled = $object->getCollectivity()->getIsServicesEnabled();
        }

        /**
         * @var User $user
         */
        $user = $this->getUser();

        $actionEnabled = true;
        if ($object instanceof CollectivityRelated && (!$this->authorizationChecker->isGranted('ROLE_ADMIN') && !$user->getServices()->isEmpty())) {
            $actionEnabled = $object->isInUserServices($this->userProvider->getAuthenticatedUser());
        }

        if (!$actionEnabled) {
            return $this->redirectToRoute($this->getRouteName('list'));
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
            'object'         => $object,
            'serviceEnabled' => $serviceEnabled,
        ]);
    }

    /**
     * The show action view
     * Display the object information.
     *
     * @param string $id The ID of the data to display
     */
    public function showAction(string $id): Response
    {
        /** @var CollectivityRelated $object */
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        if ($object instanceof Collectivity) {
            $serviceEnabled = $object->getIsServicesEnabled();
        } else {
            $serviceEnabled = $object->getCollectivity()->getIsServicesEnabled();
        }

        $actionEnabled = true;
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if ($object instanceof CollectivityRelated && !$this->authorizationChecker->isGranted('ROLE_ADMIN') && !$user->getServices()->isEmpty()) {
            $actionEnabled = $object->isInUserServices($this->userProvider->getAuthenticatedUser());
        }

        return $this->render($this->getTemplatingBasePath('show'), [
            'object'         => $object,
            'actionEnabled'  => $actionEnabled,
            'serviceEnabled' => $serviceEnabled,
        ]);
    }

    /**
     * The delete action view
     * Display a confirmation message to confirm data deletion.
     */
    public function deleteAction(string $id): Response
    {
        /** @var CollectivityRelated $object */
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        $actionEnabled = true;
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if ($object instanceof CollectivityRelated && !$this->authorizationChecker->isGranted('ROLE_ADMIN') && !$user->getServices()->isEmpty()) {
            $actionEnabled = $object->isInUserServices($this->userProvider->getAuthenticatedUser());
        }

        if (!$actionEnabled) {
            return $this->redirectToRoute($this->getRouteName('list'));
        }

        return $this->render($this->getTemplatingBasePath('delete'), [
            'object' => $object,
            'id'     => $id,
        ]);
    }

    /**
     * The deletion action
     * Delete the data.
     *
     * @throws \Exception
     */
    public function deleteConfirmationAction(string $id): Response
    {
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        if ($this->isSoftDelete()) {
            if (!\method_exists($object, 'setDeletedAt')) {
                throw new MethodNotImplementedException('setDeletedAt');
            }
            $object->setDeletedAt(new \DateTimeImmutable());
            $this->repository->update($object);
        } else {
            $this->entityManager->remove($object);
            $this->entityManager->flush();
        }

        $this->addFlash('success', $this->getFlashbagMessage('success', 'delete', $object));

        return $this->redirectToRoute($this->getRouteName('list'));
    }

    public function pdfAction(string $id)
    {
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        return new PdfResponse(
            $this->pdf->getOutputFromHtml(
                $this->renderView($this->getTemplatingBasePath('pdf'), ['object' => $object])
            ),
            $this->getPdfName((string) $object) . '.pdf'
        );
    }

    public function getPdfName(string $name): string
    {
        $name = ChainManipulator::removeAllNonAlphaNumericChar(ChainManipulator::removeAccents($name));

        return $name . '-' . date('mdY');
    }

    /**
     * Check if we have to produce a soft delete behaviour.
     */
    protected function isSoftDelete(): bool
    {
        return false;
    }

    public function getNotifications(): array
    {
        return $this->entityManager->getRepository(Notification::class)->findAll();
    }
}
