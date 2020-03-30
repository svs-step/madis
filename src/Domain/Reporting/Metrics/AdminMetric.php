<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
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

namespace App\Domain\Reporting\Metrics;

use App\Domain\User\Dictionary\CollectivityTypeDictionary;
use App\Infrastructure\ORM\Maturity;
use App\Infrastructure\ORM\Registry;
use App\Infrastructure\ORM\User;

class AdminMetric implements MetricInterface
{
    /**
     * @var User\Repository\Collectivity
     */
    private $collectivityRepository;

    /**
     * @var Registry\Repository\Mesurement
     */
    private $mesurementRepository;

    /**
     * @var Registry\Repository\Proof
     */
    private $proofRepository;

    /**
     * @var Maturity\Repository\Survey
     */
    private $surveyRepository;

    public function __construct(
        User\Repository\Collectivity $collectivityRepository,
        Registry\Repository\Mesurement $mesurementRepository,
        Registry\Repository\Proof $proofRepository,
        Maturity\Repository\Survey $surveyRepository
    ) {
        $this->collectivityRepository = $collectivityRepository;
        $this->mesurementRepository   = $mesurementRepository;
        $this->proofRepository        = $proofRepository;
        $this->surveyRepository       = $surveyRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(): array
    {
        $collectiviyByType = [];
        foreach (CollectivityTypeDictionary::getTypesKeys() as $type) {
            $collectiviyByType[$type] = 0;
        }

        $averageMesurement       = floatval($this->mesurementRepository->planifiedAverageOnAllCollectivity());
        $averageProof            = floatval($this->proofRepository->averageProofFiled());
        $averageBalanceSheetPoof = floatval($this->proofRepository->averageBalanceSheetProof());
        $averageSurveyLastYer    = floatval($this->surveyRepository->averageSurveyDuringLastYear());

        $data = [
            'collectivityByType' => [
                'value' => [
                    'all'  => 0,
                    'type' => $collectiviyByType,
                ],
            ],
            'mesurementByCollectivity' => [
                'average' => $averageMesurement,
            ],
            'proofByCollectivity' => [
                'average' => $averageProof,
            ],
            'balanceSheetProofByCollectivity' => [
                'average' => $averageBalanceSheetPoof * 100,
            ],
            'surveyLastYear' => [
                'average' => $averageSurveyLastYer * 100,
            ],
        ];

        $collectivities = $this->collectivityRepository->findAllActive();

        $data['collectivityByType']['value']['all'] = count($collectivities);
        foreach ($collectivities as $collectivity) {
            ++$data['collectivityByType']['value']['type'][$collectivity->getType()];
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateViewName(): string
    {
        return 'Reporting/Dashboard/index_admin.html.twig';
    }
}
