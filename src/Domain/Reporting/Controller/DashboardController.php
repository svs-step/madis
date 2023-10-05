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

namespace App\Domain\Reporting\Controller;

use App\Application\Interfaces\CollectivityRelated;
use App\Domain\Registry\Repository\Mesurement;
use App\Domain\Reporting\Handler\ExportCsvHandler;
use App\Domain\Reporting\Handler\MetricsHandler;
use App\Infrastructure\ORM\Maturity\Repository\Referentiel;
use App\Infrastructure\ORM\Maturity\Repository\Survey;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class DashboardController extends AbstractController
{
    /**
     * @var MetricsHandler
     */
    private $metricsHandler;

    /**
     * @var ExportCsvHandler
     */
    private $exportCsvHandler;

    private Mesurement $repository;

    private Referentiel $referentielRepository;
    private Survey $surveyRepository;

    public function __construct(
        MetricsHandler $metricsHandler,
        ExportCsvHandler $exportCsvHandler,
        Mesurement $repository,
        Referentiel $referentielRepository,
        Survey $surveyRepository
    ) {
        $this->repository            = $repository;
        $this->metricsHandler        = $metricsHandler;
        $this->exportCsvHandler      = $exportCsvHandler;
        $this->referentielRepository = $referentielRepository;
        $this->surveyRepository      = $surveyRepository;
    }

    /**
     * Get dashboard index page.
     * Compute every metrics to display.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $metrics      = $this->metricsHandler->getHandler();
        $actions      = [];
        $referentiels = $this->referentielRepository->findAll();
        if (!$this->isGranted('ROLE_REFERENT')) {
            $user         = $this->getUser();
            $collectivity = $user instanceof CollectivityRelated ? $user->getCollectivity() : null;
            $actions      = $this->repository->getPlanifiedActionsDashBoard($this->getParameter('APP_USER_DASHBOARD_ACTION_PLAN_LIMIT'), $collectivity);
            $referentiels = array_reduce(
                array_map(
                    function (\App\Domain\Maturity\Model\Survey $survey) {
                        return $survey->getReferentiel();
                    }, $this->surveyRepository->findAllByCollectivity($collectivity, ['createdAt' => 'DESC'])
                ),
                function (array $result, \App\Domain\Maturity\Model\Referentiel $referentiel) {
                    if (!isset($result[$referentiel->getId()->toString()])) {
                        $result[$referentiel->getId()->toString()] = $referentiel;
                    }

                    return $result;
                },
                []);

            $referentiels = array_values($referentiels);
        }
        $selectedRef = $referentiels[0] ?? null;
        if ($request->get('referentiel')) {
            $selRefs = array_values(array_filter($referentiels, function (\App\Domain\Maturity\Model\Referentiel $referentiel) use ($request) {
                return $referentiel->getId()->toString() === $request->get('referentiel');
            }));

            if (count($selRefs) > 0) {
                $selectedRef = $selRefs[0];
            }
        }

        return $this->render($metrics->getTemplateViewName(), [
            'data'         => $metrics->getData($selectedRef),
            'actions'      => $actions,
            'referentiels' => $referentiels,
            'selected_ref' => $selectedRef,
        ]);
    }

    /**
     * Generate CSV file for collectivity or treatment.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportCsvAction(string $exportType)
    {
        if (!$this->isGranted('ROLE_REFERENT')) {
            throw new AccessDeniedHttpException('You can\'t access to csv export');
        }

        return $this->exportCsvHandler->generateCsv($exportType);
    }
}
