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
use App\Domain\User\Dictionary\UserRoleDictionary;
use Symfony\Component\Security\Core\Security;

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

    /**
     * @var Security
     */
    private $security;

    public function __construct(
        User\Repository\Collectivity $collectivityRepository,
        Registry\Repository\Mesurement $mesurementRepository,
        Registry\Repository\Proof $proofRepository,
        Maturity\Repository\Survey $surveyRepository,
        Registry\Repository\Treatment $treatmentRepository,
        Security $security
    ) {
        $this->collectivityRepository = $collectivityRepository;
        $this->mesurementRepository   = $mesurementRepository;
        $this->proofRepository        = $proofRepository;
        $this->surveyRepository       = $surveyRepository;
        $this->treatmentRepository    = $treatmentRepository;
        $this->security               = $security;
    }

    public function getData(): array
    {
        if (!$this->security->isGranted(UserRoleDictionary::ROLE_ADMIN)) {
            $collectivities = $this->collectivityRepository->findByUserReferent($this->security->getUser());
        } else {
            $collectivities = $this->collectivityRepository->findAllActive();
        }

        $collectivityByType = [];
        foreach (CollectivityTypeDictionary::getTypesKeys() as $type) {
            $collectivityByType[$type] = 0;
        }

        $averageMesurement       = floatval($this->mesurementRepository->planifiedAverageOnAllCollectivity($collectivities));
        $averageProof            = floatval($this->proofRepository->averageProofFiled($collectivities));
        $averageBalanceSheetPoof = floatval($this->proofRepository->averageBalanceSheetProof($collectivities));
        $averageSurveyLastYer    = floatval($this->surveyRepository->averageSurveyDuringLastYear($collectivities));

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
                    'all'          => $totalCollectivity,
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
        $inseeValidType   = [CollectivityTypeDictionary::TYPE_COMMUNE, CollectivityTypeDictionary::TYPE_CCAS, CollectivityTypeDictionary::TYPE_CIAS, CollectivityTypeDictionary::TYPE_MEDICAL_INSTITUTION, CollectivityTypeDictionary::TYPE_OTHER];
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
            }

            if (false === $collectivity->isDifferentDpo()) {
                ++$nbIsDifferentDpo;
            }

            if (isset($data['collectivityByType']['value']['type'][$collectivity->getType()])) {
                ++$data['collectivityByType']['value']['type'][$collectivity->getType()];
            }
        }

        if ($totalCollectivity > 0) {
            $data['collectivityByAddressInsee']['value']['dpoPercent'] = floor(($nbIsDifferentDpo * 100) / $totalCollectivity);
        }

        return $data;
    }

    public function getTemplateViewName(): string
    {
        return 'Reporting/Dashboard/index_admin.html.twig';
    }
}
