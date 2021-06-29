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

namespace App\Domain\User\Controller;

use App\Application\Controller\CRUDController;
use App\Application\Traits\ServersideDatatablesTrait;
use App\Domain\User\Dictionary\UserRoleDictionary;
use App\Domain\User\Form\Type\UserType;
use App\Domain\User\Model;
use App\Domain\User\Repository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Exception\MethodNotImplementedException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property Repository\User $repository
 */
class UserController extends CRUDController
{
    use ServersideDatatablesTrait;
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var Security
     */
    protected $security;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\User $repository,
        RequestStack $requestStack,
        EncoderFactoryInterface $encoderFactory,
        Pdf $pdf,
        RouterInterface $router,
        Security $security
    ) {
        parent::__construct($entityManager, $translator, $repository, $pdf);
        $this->requestStack   = $requestStack;
        $this->encoderFactory = $encoderFactory;
        $this->router         = $router;
        $this->security       = $security;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDomain(): string
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModel(): string
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\User::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormType(): string
    {
        return UserType::class;
    }

    protected function isSoftDelete(): bool
    {
        return true;
    }

    /**
     * The unarchive action view
     * Display a confirmation message to confirm data un-archivage.
     */
    public function unarchiveAction(string $id): Response
    {
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        return $this->render($this->getTemplatingBasePath('unarchive'), [
            'object' => $object,
        ]);
    }

    /**
     * The unarchive action
     * Unarchive the data.
     *
     * @throws \Exception
     */
    public function unarchiveConfirmationAction(string $id): Response
    {
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        if (!\method_exists($object, 'setDeletedAt')) {
            throw new MethodNotImplementedException('setDeletedAt');
        }
        $object->setDeletedAt(null);
        $this->repository->update($object);

        $this->addFlash('success', $this->getFlashbagMessage('success', 'unarchive', $object));

        return $this->redirectToRoute($this->getRouteName('list'));
    }

    public function listAction(): Response
    {
        $criteria = $this->getRequestCriteria();

        return $this->render($this->getTemplatingBasePath('list'), [
            'totalItem' => $this->repository->count($criteria),
            'route'     => $this->router->generate('user_user_list_datatables', ['archive' => $criteria['archive']]),
        ]);
    }

    public function listDataTables(Request $request): JsonResponse
    {
        $criteria    = $this->getRequestCriteria();
        $users       = $this->getResults($request, $criteria);
        $reponse     = $this->getBaseDataTablesResponse($request, $users, $criteria);

        /** @var Model\User $user */
        foreach ($users as $user) {
            $roles = '';
            foreach ($user->getRoles() as $role) {
                $span = '<span class="badge ' . $this->getRolesColor($role) . '">' . UserRoleDictionary::getRoles()[$role] . '</span>';
                $roles .= $span;
            }

            $userActifBgColor = 'bg-green';
            if (!$user->isEnabled()) {
                $userActifBgColor = 'bg-red';
            }

            $collectivityActifBgColor = 'bg-green';
            if (!$user->getCollectivity()->isActive()) {
                $userActifBgColor = 'bg-red';
            }

            $actif = '
                <span class="badge ' . $userActifBgColor . '">' . $this->translator->trans('user.user.title.label') . '</span>
                <span class="badge ' . $collectivityActifBgColor . '">' . $this->translator->trans('user.collectivity.title.label') . '</span>'
            ;

            $europeTimezone    = new \DateTimeZone('Europe/Paris');
            $reponse['data'][] = [
                'prenom'       => $user->getFirstName(),
                'nom'          => $user->getLastName(),
                'email'        => $user->getEmail(),
                'collectivite' => $user->getCollectivity()->getName(),
                'roles'        => $roles,
                'actif'        => $actif,
                'connexion'    => !\is_null($user->getLastLogin()) ? $user->getLastLogin()->setTimezone($europeTimezone)->format('Y-m-d H:i:s') : null,
                'actions'      => $this->getActionCellsContent($user),
            ];
        }

        $jsonResponse = new JsonResponse();
        $jsonResponse->setJson(\json_encode($reponse));

        return $jsonResponse;
    }

    protected function getLabelAndKeysArray(): array
    {
        return [
            0 => 'prenom',
            1 => 'nom',
            2 => 'email',
            3 => 'collectivite',
            4 => 'roles',
            5 => 'actif',
            6 => 'connexion',
            7 => 'actions',
        ];
    }

    private function getRequestCriteria()
    {
        $criteria            = [];
        $request             = $this->requestStack->getMasterRequest();
        $criteria['archive'] = $request->query->getBoolean('archive');

        if (!$this->security->isGranted('ROLE_ADMIN')) {
            /** @var Model\User $user */
            $user                              = $this->security->getUser();
            $criteria['collectivitesReferees'] = $user->getCollectivitesReferees();
        }

        return $criteria;
    }

    private function getActionCellsContent(Model\User $user)
    {
        $cellContent = '';
        if ($this->security->getUser() !== $user && \is_null($user->getDeletedAt()) && !$this->isGranted('ROLE_PREVIOUS_ADMIN')) {
            $cellContent .=
                '<a href="' . $this->router->generate('reporting_dashboard_index', ['_switch_user' => $user->getUsername()]) . '">
                    <i class="fa fa-user-lock"></i> ' .
                $this->translator->trans('action.impersonate') .
                '</a>';
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            if (\is_null($user->getDeletedAt())) {
                $cellContent .=
                    '<a href="' . $this->router->generate('user_user_edit', ['id' => $user->getId()]) . '">
                        <i class="fa fa-pencil-alt"></i> ' .
                    $this->translator->trans('action.edit') .
                    '</a>';
            }

            if ($this->security->getUser() !== $user) {
                if (\is_null($user->getDeletedAt())) {
                    $cellContent .=
                        '<a href="' . $this->router->generate('user_user_delete', ['id' => $user->getId()]) . '">
                        <i class="fa fa-archive"></i> ' .
                        $this->translator->trans('action.archive') .
                        '</a>';
                } else {
                    $cellContent .=
                        '<a href="' . $this->router->generate('user_user_unarchive', ['id' => $user->getId()]) . '">
                        <i class="fa fa-archive"></i> ' .
                        $this->translator->trans('action.unarchive') .
                        '</a>';
                }

                $cellContent .=
                '<a href="' . $this->router->generate('user_user_delete', ['id' => $user->getId()]) . '">
                    <i class="fa fa-trash-alt"></i> ' .
                $this->translator->trans('action.delete') .
                '</a>';
            }
        }

        return $cellContent;
    }

    private function getRolesColor(string $role)
    {
        switch ($role) {
            case UserRoleDictionary::ROLE_ADMIN:
                return 'bg-red';
            case UserRoleDictionary::ROLE_USER:
                return 'bg-blue';
            default:
                return 'bg-red';
        }
    }
}
