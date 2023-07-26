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
use App\Domain\Registry\Dictionary\ViolationCauseDictionary;
use App\Domain\Registry\Dictionary\ViolationGravityDictionary;
use App\Domain\Registry\Dictionary\ViolationNatureDictionary;
use App\Domain\Registry\Dictionary\ViolationNotificationListDictionary;
use App\Domain\Registry\Form\Type\ViolationType;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\Reporting\Handler\WordHandler;
use App\Domain\User\Dictionary\UserRoleDictionary;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property Repository\Violation $repository
 */
class ViolationController extends CRUDController
{
    use ServersideDatatablesTrait;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var WordHandler
     */
    protected $wordHandler;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var UserProvider
     */
    protected $userProvider;

    /**
     * @var RouterInterface
     */
    protected $router;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\Violation $repository,
        RequestStack $requestStack,
        WordHandler $wordHandler,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProvider $userProvider,
        Pdf $pdf,
        RouterInterface $router
    ) {
        parent::__construct($entityManager, $translator, $repository, $pdf, $userProvider, $authorizationChecker);
        $this->requestStack         = $requestStack;
        $this->wordHandler          = $wordHandler;
        $this->authorizationChecker = $authorizationChecker;
        $this->userProvider         = $userProvider;
        $this->router               = $router;
    }

    protected function getDomain(): string
    {
        return 'registry';
    }

    protected function getModel(): string
    {
        return 'violation';
    }

    protected function getModelClass(): string
    {
        return Model\Violation::class;
    }

    protected function getFormType(): string
    {
        return ViolationType::class;
    }

    protected function isSoftDelete(): bool
    {
        return true;
    }

    /**
     * Generate a word report of contractors.
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public function reportAction(): Response
    {
        $objects = $this->repository->findAllByCollectivity(
            $this->userProvider->getAuthenticatedUser()->getCollectivity(),
            false,
            ['date' => 'asc']
        );

        return $this->wordHandler->generateRegistryViolationReport($objects);
    }

    public function listAction(): Response
    {
        $criteria = $this->getRequestCriteria();

        $category = $this->entityManager->getRepository(Category::class)->findOneBy([
            'name' => 'Violation',
        ]);

        return $this->render($this->getTemplatingBasePath('list'), [
            'totalItem' => $this->repository->count($criteria),
            'category'  => $category,
            'route'     => $this->router->generate('registry_violation_list_datatables', ['archive' => $criteria['archive']]),
        ]);
    }

    public function listDataTables(Request $request): JsonResponse
    {
        $criteria = $this->getRequestCriteria();
        $users    = $this->getResults($request, $criteria);
        $reponse  = $this->getBaseDataTablesResponse($request, $users, $criteria);

        $yes = '<span class="badge bg-red">' . $this->translator->trans('label.yes') . '</span>';
        $no  = '<span class="badge bg-green">' . $this->translator->trans('label.no') . '</span>';

        /** @var Model\Violation $violation */
        foreach ($users as $violation) {
            $violationLink = '<a aria-label="' . \date_format($violation->getDate(), 'd/m/Y') . '" href="' . $this->router->generate('registry_violation_show', ['id' => $violation->getId()->toString()]) . '">
                ' . \date_format($violation->getDate(), 'd/m/Y') . '
            </a>';

            $allNatures   = ViolationNatureDictionary::getNatures();
            $natures      = '';
            $naturesArray = new ArrayCollection($violation->getViolationNatures());

            if (count($naturesArray) > 0) {
                $natures = $naturesArray->map(function ($name) use ($allNatures) {
                    return $allNatures[$name] ?? null;
                })->filter(function ($r) {return null !== $r; });

                $natures = join(', ', $natures->toArray());
            }

            $reponse['data'][] = [
                'id'           => $violation->getId(),
                'collectivite' => $violation->getCollectivity()->getName(),
                'date'         => $violationLink,
                'nature'       => $natures,
                'cause'        => !\is_null($violation->getCause()) ? ViolationCauseDictionary::getNatures()[$violation->getCause()] : null,
                'gravity'      => !\is_null($violation->getGravity()) ? ViolationGravityDictionary::getGravities()[$violation->getGravity()] : null,
                'createdAt'    => date_format($violation->getCreatedAt(), 'd-m-Y H:i'),
                'updatedAt'    => date_format($violation->getUpdatedAt(), 'd-m-Y H:i'),
                'inProgress'   => $violation->isInProgress() ? $yes : $no,
                'actions'      => $this->getActionCellsContent($violation),
                'notification' => !\is_null($violation->getNotification()) && isset(ViolationNotificationListDictionary::getNotificationsList()[$violation->getNotification()]) ? ViolationNotificationListDictionary::getNotificationsList()[$violation->getNotification()] : null,
            ];
        }

        $jsonResponse = new JsonResponse();
        $jsonResponse->setJson(\json_encode($reponse));

        return $jsonResponse;
    }

    private function isRequestInUserServices(Model\Violation $violation): bool
    {
        $user = $this->userProvider->getAuthenticatedUser();

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return $violation->isInUserServices($user);
    }

    protected function getLabelAndKeysArray(): array
    {
        return [
            0 => 'date',
            1 => 'collectivite',
            2 => 'nature',
            3 => 'inProgress',
            4 => 'cause',
            5 => 'gravity',
            6 => 'notification',
            7 => 'createdAt',
            8 => 'updatedAt',
            9 => 'actions',
        ];
    }

    private function getActionCellsContent(Model\Violation $violation)
    {
        $cellContent = '';
        $user        = $this->userProvider->getAuthenticatedUser();
        if ($this->authorizationChecker->isGranted('ROLE_USER')
        && \is_null($violation->getDeletedAt())
        && ($user->getServices()->isEmpty() || $this->isRequestInUserServices($violation))) {
            $cellContent .= '<a aria-label="' . $this->translator->trans('action.edit') . '" href="' . $this->router->generate('registry_violation_edit', ['id' => $violation->getId()]) . '">
                    <i class="fa fa-pencil-alt"></i> ' .
                    $this->translator->trans('action.edit') . '
                </a>
                <a aria-label="' . $this->translator->trans('action.archive') . '" href="' . $this->router->generate('registry_violation_delete', ['id' => $violation->getId()]) . '">
                    <i class="fa fa-archive"></i> ' .
                    $this->translator->trans('action.archive') . '
                </a>';
        }

        return $cellContent;
    }

    private function getRequestCriteria()
    {
        $criteria            = [];
        $request             = $this->requestStack->getMasterRequest();
        $criteria['archive'] = $request->query->getBoolean('archive');
        $user                = $this->userProvider->getAuthenticatedUser();

        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $criteria['collectivity'] = $user->getCollectivity();
        }

        if (\in_array(UserRoleDictionary::ROLE_REFERENT, $user->getRoles())) {
            $criteria['collectivity'] = $user->getCollectivitesReferees();
        }

        return $criteria;
    }
}
