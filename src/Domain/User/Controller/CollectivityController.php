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
use App\Domain\User\Dictionary\CollectivityTypeDictionary;
use App\Domain\User\Form\Type\CollectivityType;
use App\Domain\User\Model;
use App\Domain\User\Repository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property Repository\Collectivity $repository
 */
class CollectivityController extends CRUDController
{
    use ServersideDatatablesTrait;

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
        Repository\Collectivity $repository,
        Pdf $pdf,
        RouterInterface $router,
        Security $security
    ) {
        parent::__construct($entityManager, $translator, $repository, $pdf);
        $this->router       = $router;
        $this->security     = $security;
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
        return 'collectivity';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\Collectivity::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormType(): string
    {
        return CollectivityType::class;
    }

    public function listAction(): Response
    {
        $criteria = $this->getRequestCriteria();

        return $this->render($this->getTemplatingBasePath('list'), [
            'totalItem' => $this->repository->count($criteria),
            'route'     => $this->router->generate('user_collectivity_list_datatables'),
        ]);
    }

    public function listDataTables(Request $request): JsonResponse
    {
        $criteria       = $this->getRequestCriteria();
        $collectivities = $this->getResults($request, $criteria);
        $reponse        = $this->getBaseDataTablesResponse($request, $collectivities, $criteria);

        $active   = '<span class="badge bg-green">' . $this->translator->trans('label.active') . '</span>';
        $inactive = '<span class="badge bg-red">' . $this->translator->trans('label.inactive') . '</span>';
        /** @var Model\Collectivity $collectivity */
        foreach ($collectivities as $collectivity) {
            $reponse['data'][] = [
                'nom'       => '<a href="' . $this->router->generate('user_collectivity_show', ['id' => $collectivity->getId()]) . '">' .
                                    $collectivity->getName() .
                                '</a>',
                'nom_court' => $collectivity->getShortName(),
                'type'      => !\is_null($collectivity->getType()) ? CollectivityTypeDictionary::getTypes()[$collectivity->getType()] : null,
                'siren'     => $collectivity->getSiren(),
                'statut'    => $collectivity->isActive() ? $active : $inactive,
                'actions'   => $this->getActionCellsContent($collectivity),
            ];
        }

        $jsonResponse = new JsonResponse();
        $jsonResponse->setJson(\json_encode($reponse));

        return $jsonResponse;
    }

    private function getActionCellsContent(Model\Collectivity $collectivity)
    {
        $cellContent = '<a href="' . $this->router->generate('user_collectivity_edit', ['id'=> $collectivity->getId()]) . '">
            <i class="fa fa-pencil-alt"></i> ' .
            $this->translator->trans('action.edit') .
        '</a>';

        if (0 === \count($collectivity->getUsers())) {
            $cellContent .= '<a href="' . $this->router->generate('user_collectivity_delete', ['id'=> $collectivity->getId()]) . '">
                <i class="fa fa-trash"></i> ' .
                $this->translator->trans('action.delete') .
            '</a>';
        }

        return $cellContent;
    }

    protected function getLabelAndKeysArray(): array
    {
        return [
            0 => 'nom',
            1 => 'nom_court',
            2 => 'type',
            3 => 'siren',
            4 => 'statut',
            5 => 'actions',
        ];
    }

    private function getRequestCriteria()
    {
        $criteria            = [];

        if (!$this->security->isGranted('ROLE_ADMIN')) {
            /** @var Model\User $user */
            $user                              = $this->security->getUser();
            $criteria['collectivitesReferees'] = $user->getCollectivitesReferees();
        }

        return $criteria;
    }
}
