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

use App\Domain\Maturity;
use App\Domain\Registry;
use App\Domain\User;
use App\Domain\User\Dictionary\CollectivityTypeDictionary;

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

    /**
     * @var Registry\Repository\Treatment
     */
    private $treatmentRepository;

    public function __construct(
        User\Repository\Collectivity $collectivityRepository,
        Registry\Repository\Mesurement $mesurementRepository,
        Registry\Repository\Proof $proofRepository,
        Maturity\Repository\Survey $surveyRepository,
        Registry\Repository\Treatment $treatmentRepository
    ) {
        $this->collectivityRepository = $collectivityRepository;
        $this->mesurementRepository   = $mesurementRepository;
        $this->proofRepository        = $proofRepository;
        $this->surveyRepository       = $surveyRepository;
        $this->treatmentRepository    = $treatmentRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(): array
    {
        $collectivityByType = [];
        foreach (CollectivityTypeDictionary::getTypesKeys() as $type) {
            $collectivityByType[$type] = 0;
        }

        $averageMesurement       = floatval($this->mesurementRepository->planifiedAverageOnAllCollectivity());
        $averageProof            = floatval($this->proofRepository->averageProofFiled());
        $averageBalanceSheetPoof = floatval($this->proofRepository->averageBalanceSheetProof());
        $averageSurveyLastYer    = floatval($this->surveyRepository->averageSurveyDuringLastYear());
        $collectivities          = $this->collectivityRepository->findAllActive();

        $totalCollectivity = count($collectivities);

        $data = [
            'collectivityByType' => [
                'value' => [
                    'all'  => $totalCollectivity,
                    'type' => $collectivityByType,
                ],
            ],
            'collectivityByAddressInsee' => [
                'value' => [
                    'all'          => 0,
                    'addressInsee' => [],
                    'dpoPercent'   => 0,
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

        $nbIsDifferentDpo = 0;
        $inseeCount       = 0;
        $inseeValidType   = [CollectivityTypeDictionary::TYPE_COMMUNE, CollectivityTypeDictionary::TYPE_CCAS, CollectivityTypeDictionary::TYPE_OTHER];
        foreach ($collectivities as $collectivity) {
            if (!\is_null($collectivity->getAddress())
                && !\is_null($collectivity->getAddress()->getInsee())
                && \in_array($collectivity->getType(), $inseeValidType)
            ) {
                $collectivityData = [
                    'name'                => $collectivity->getShortName(),
                    'nbTraitementActifs'  => intval($this->treatmentRepository->countAllActiveByCollectivity($collectivity)),
                    'nbActionsProtection' => intval($this->mesurementRepository->countAppliedByCollectivity($collectivity)),
                ];
                $collectivityInsee                                                                 = $collectivity->getAddress()->getInsee();
                $data['collectivityByAddressInsee']['value']['addressInsee'][$collectivityInsee][] = $collectivityData;
                ++$inseeCount;
            }

            if (false === $collectivity->isDifferentDpo()) {
                ++$nbIsDifferentDpo;
            }

            ++$data['collectivityByType']['value']['type'][$collectivity->getType()];
        }

        if ($inseeCount > 0) {
            $data['collectivityByAddressInsee']['value']['all']        = $inseeCount;
            $data['collectivityByAddressInsee']['value']['dpoPercent'] = round(($nbIsDifferentDpo * 100) / $inseeCount);
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
