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

use App\Domain\Registry\Repository\Mesurement;
use App\Domain\Reporting\Handler\ExportCsvHandler;
use App\Domain\Reporting\Handler\MetricsHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    public function __construct(MetricsHandler $metricsHandler, ExportCsvHandler $exportCsvHandler, Mesurement $repository)
    {
        $this->repository       = $repository;
        $this->metricsHandler   = $metricsHandler;
        $this->exportCsvHandler = $exportCsvHandler;
    }

    /**
     * Get dashboard index page.
     * Compute every metrics to display.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $metrics = $this->metricsHandler->getHandler();

        $actions = $this->repository->getPlanifiedActionsDashBoard();

        return $this->render($metrics->getTemplateViewName(), [
            'data'          => $metrics->getData(),
            'actions'       => $actions,
            'limit_actions' => $_ENV['APP_USER_DASHBOARD_ACTION_PLAN_LIMIT'],
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
