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

use App\Application\Symfony\Security\UserProvider;
use App\Domain\Maturity\Model\Referentiel;
use App\Domain\Registry\Calculator\Completion\ConformiteTraitementCompletion;
use App\Domain\Registry\Dictionary\ConformiteTraitementLevelDictionary;
use App\Domain\Registry\Dictionary\MesurementStatusDictionary;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\Registry\Service\ConformiteOrganisationService;
use App\Domain\Reporting\Dictionary\LogJournalSubjectDictionary;
use App\Domain\Reporting\Repository\LogJournal;
use App\Infrastructure\ORM\Maturity\Repository\Survey as SurveyRepository;
use App\Infrastructure\ORM\Registry\Repository\ConformiteOrganisation\Evaluation;
use Doctrine\Inflector\InflectorFactory;
use Doctrine\Inflector\Language;
use Doctrine\ORM\EntityManagerInterface;

class UserMetric implements MetricInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Repository\ConformiteTraitement\ConformiteTraitement
     */
    private $conformiteTraitementRepository;

    /**
     * @var Evaluation
     */
    private $evaluationRepository;

    /**
     * @var Repository\Request
     */
    private $requestRepository;

    /**
     * @var Repository\Treatment
     */
    private $treatmentRepository;
    /**
     * @var SurveyRepository
     */
    private $surveyRepository;

    /**
     * @var UserProvider
     */
    private $userProvider;

    /**
     * @var LogJournal
     */
    private $logJournalRepository;

    /**
     * @var int
     */
    private $userLogJounalViewLimit;

    public function __construct(
        EntityManagerInterface $entityManager,
        Repository\ConformiteTraitement\ConformiteTraitement $conformiteTraitementRepository,
        Repository\Request $requestRepository,
        Repository\Treatment $treatmentRepository,
        UserProvider $userProvider,
        Evaluation $evaluationRepository,
        LogJournal $logJournalRepository,
        SurveyRepository $surveyRepository,
        int $userLogJounalViewLimit
    ) {
        $this->entityManager                  = $entityManager;
        $this->conformiteTraitementRepository = $conformiteTraitementRepository;
        $this->requestRepository              = $requestRepository;
        $this->treatmentRepository            = $treatmentRepository;
        $this->userProvider                   = $userProvider;
        $this->evaluationRepository           = $evaluationRepository;
        $this->logJournalRepository           = $logJournalRepository;
        $this->surveyRepository               = $surveyRepository;
        $this->userLogJounalViewLimit         = $userLogJounalViewLimit;
    }

    public function getData(Referentiel $referentiel = null): array
    {
        $data = [
            'conformiteOrganisation' => [
            ],
            'conformiteTraitement' => [
                'data'   => [],
                'labels' => [],
                'colors' => [],
            ],
            'contractor' => [
                'all'     => 0,
                'clauses' => [
                    'yes' => 0,
                    'no'  => 0,
                ],
                'adoptedSecurityFeatures' => [
                    'yes' => 0,
                    'no'  => 0,
                ],
                'maintainsTreatmentRegister' => [
                    'yes' => 0,
                    'no'  => 0,
                ],
                'sendingDataOutsideEu' => [
                    'yes' => 0,
                    'no'  => 0,
                ],
            ],
            'maturity'   => [],
            'mesurement' => [
                'value' => [
                    'applied'       => 0,
                    'notApplied'    => 0,
                    'notApplicable' => 0,
                    'planified'     => 0,
                ],
                'percent' => [
                    'applied'       => 0,
                    'notApplied'    => 0,
                    'notApplicable' => 0,
                    'planified'     => 0,
                ],
            ],
            'request' => [
                'value' => [
                    'all'  => 0,
                    'type' => [
                        'correct'         => 0,
                        'delete'          => 0,
                        'withdrawConsent' => 0,
                        'access'          => 0,
                        'dataPortability' => 0,
                        'limitTreatment'  => 0,
                        'other'           => 0,
                    ],
                    'status' => [
                        'toProcess'  => 0,
                        'processed'  => 0,
                        'incomplete' => 0,
                    ],
                ],
            ],
            'treatment' => [
                'value' => [
                    'active'  => 0,
                    'numeric' => 0,
                    'data'    => [
                        'securityAccessControl' => [
                            'yes' => 0,
                            'no'  => 0,
                        ],
                        'securityTracability' => [
                            'yes' => 0,
                            'no'  => 0,
                        ],
                        'securitySaving' => [
                            'yes' => 0,
                            'no'  => 0,
                        ],
                        'securityUpdate' => [
                            'yes' => 0,
                            'no'  => 0,
                        ],
                    ],
                ],
            ],
            'violation' => [
                'value' => [
                    'all' => 0,
                ],
            ],
            'aipd' => [
                'toDo' => 0,
            ],
        ];

        $collectivity = $this->userProvider->getAuthenticatedUser()->getCollectivity();

        $conformiteOrganisationEvaluation = $this->evaluationRepository->findLastByOrganisation($collectivity);

        $contractors = $this->entityManager->getRepository(Model\Contractor::class)->findBy(
            ['collectivity' => $collectivity]
        );

        $data['logJournal'] = $this->logJournalRepository
            ->findAllByCollectivityWithoutSubjects(
                $collectivity,
                $this->userLogJounalViewLimit,
                [
                    LogJournalSubjectDictionary::USER_COLLECTIVITY,
                    LogJournalSubjectDictionary::USER_EMAIL,
                    LogJournalSubjectDictionary::USER_FIRSTNAME,
                    LogJournalSubjectDictionary::USER_LASTNAME,
                    LogJournalSubjectDictionary::USER_PASSWORD,
                    LogJournalSubjectDictionary::USER_USER,
                    LogJournalSubjectDictionary::ADMIN_DUPLICATION,
                ]
            );

        if ($referentiel) {
            $maturity = $this->surveyRepository->findLatestByReferentialAndCollectivity(
                $referentiel,
                $collectivity
            );
        }

        $mesurements = $this->entityManager->getRepository(Model\Mesurement::class)->findBy(
            ['collectivity' => $collectivity]
        );
        $requests   = $this->requestRepository->findAllByCollectivity($collectivity);
        $treatments = $this->entityManager->getRepository(Model\Treatment::class)->findBy(
            ['collectivity' => $collectivity]
        );
        $violations = $this->entityManager->getRepository(Model\Violation::class)->findBy(
            ['collectivity' => $collectivity]
        );

        // =========================
        // PROCESS DATA MANIPULATION
        // =========================

        // CONTRACTOR
        /** @var Model\Contractor $contractor */
        foreach ($contractors as $contractor) {
            ++$data['contractor']['all'];
            if ($contractor->isContractualClausesVerified()) {
                ++$data['contractor']['clauses']['yes'];
            } else {
                ++$data['contractor']['clauses']['no'];
            }
            if ($contractor->isAdoptedSecurityFeatures()) {
                ++$data['contractor']['adoptedSecurityFeatures']['yes'];
            } else {
                ++$data['contractor']['adoptedSecurityFeatures']['no'];
            }
            if ($contractor->isMaintainsTreatmentRegister()) {
                ++$data['contractor']['maintainsTreatmentRegister']['yes'];
            } else {
                ++$data['contractor']['maintainsTreatmentRegister']['no'];
            }
            if ($contractor->isSendingDataOutsideEu()) {
                ++$data['contractor']['sendingDataOutsideEu']['yes'];
            } else {
                ++$data['contractor']['sendingDataOutsideEu']['no'];
            }
        }

        // MATURITY
        if (isset($maturity) && isset($maturity[0]) && null !== $maturity[0]->getReferentiel()) {
            $data['maturity']['new']['name'] = $maturity[0]->getCreatedAt()->format('d/m/Y');
            foreach ($maturity[0]->getMaturity() as $item) {
                $data['maturity']['new']['data'][$item->getDomain()->getPosition()]['name']  = $item->getDomain()->getName();
                $data['maturity']['new']['data'][$item->getDomain()->getPosition()]['score'] = $item->getScore() / 10;
            }
            \ksort($data['maturity']['new']['data']);
        }
        if (isset($maturity) && isset($maturity[1]) && null !== $maturity[0]->getReferentiel()) {
            $data['maturity']['old']['name'] = $maturity[1]->getCreatedAt()->format('d/m/Y');
            foreach ($maturity[1]->getMaturity() as $item) {
                $data['maturity']['old']['data'][$item->getDomain()->getPosition()]['name']  = $item->getDomain()->getName();
                $data['maturity']['old']['data'][$item->getDomain()->getPosition()]['score'] = $item->getScore() / 10;
            }
            \ksort($data['maturity']['old']['data']);
        }

        // MESUREMENT
        foreach ($mesurements as $mesurement) {
            switch ($mesurement->getStatus()) {
                case MesurementStatusDictionary::STATUS_APPLIED:
                    $data['mesurement']['value']['applied']++;
                    break;
                case MesurementStatusDictionary::STATUS_NOT_APPLIED:
                    $data['mesurement']['value']['notApplied']++;
                    if (!\is_null($mesurement->getPlanificationDate())) {
                        ++$data['mesurement']['value']['planified'];
                    }
                    break;
                case MesurementStatusDictionary::STATUS_NOT_APPLICABLE:
                    $data['mesurement']['value']['notApplicable']++;
            }
        }
        // Only percent if there is non zero values
        if (0 < $data['mesurement']['value']['applied']) {
            $data['mesurement']['percent']['applied'] = $data['mesurement']['value']['applied'] * 100 / (\count($mesurements) - $data['mesurement']['value']['notApplicable']);
        }
        if (0 < $data['mesurement']['value']['notApplied']) {
            $data['mesurement']['percent']['notApplied'] = $data['mesurement']['value']['notApplied'] * 100 / (\count($mesurements) - $data['mesurement']['value']['notApplicable']);
        }
        if (0 < $data['mesurement']['value']['notApplicable']) {
            $data['mesurement']['percent']['notApplicable'] = $data['mesurement']['value']['notApplicable'] * 100 / \count($mesurements);
        }
        if (0 < $data['mesurement']['value']['planified']) {
            $data['mesurement']['percent']['planified'] = $data['mesurement']['value']['planified'] * 100 / (\count($mesurements) - $data['mesurement']['value']['notApplicable']);
        }

        // REQUEST
        $data['request']['value']['all'] = \count($requests);
        foreach ($requests as $request) {
            // Only take under account active requests
            if (!\is_null($request->getDeletedAt())) {
                continue;
            }

            // Type
            if ($request->getObject()) {
                $inflector = InflectorFactory::createForLanguage(Language::FRENCH)->build();
                if (isset($data['request']['value']['type'][$inflector->camelize($request->getObject())])) {
                    ++$data['request']['value']['type'][$inflector->camelize($request->getObject())];
                } else {
                    $data['request']['value']['type'][$inflector->camelize($request->getObject())] = 1;
                }
            }

            // Status
            if ($request->isComplete() && $request->isLegitimateApplicant() && $request->isLegitimateRequest()) {
                if (\is_null($request->getAnswer()->getDate())) {
                    ++$data['request']['value']['status']['toProcess'];
                } else {
                    ++$data['request']['value']['status']['processed'];
                }
            } else {
                ++$data['request']['value']['status']['incomplete'];
            }
        }

        // TREATMENT
        foreach ($treatments as $treatment) {
            // Only take under account active treatments
            if (!$treatment->isActive()) {
                continue;
            }

            ++$data['treatment']['value']['active'];

            // Numeric treatment
            if (!\is_null($treatment->getToolsString()) && strlen($treatment->getToolsString()) > 0) {
                ++$data['treatment']['value']['numeric'];

                if ($treatment->getSecurityAccessControl()->isCheck()) {
                    ++$data['treatment']['value']['data']['securityAccessControl']['yes'];
                } else {
                    ++$data['treatment']['value']['data']['securityAccessControl']['no'];
                }
                if ($treatment->getSecurityTracability()->isCheck()) {
                    ++$data['treatment']['value']['data']['securityTracability']['yes'];
                } else {
                    ++$data['treatment']['value']['data']['securityTracability']['no'];
                }
                if ($treatment->getSecuritySaving()->isCheck()) {
                    ++$data['treatment']['value']['data']['securitySaving']['yes'];
                } else {
                    ++$data['treatment']['value']['data']['securitySaving']['no'];
                }
                if ($treatment->getSecurityUpdate()->isCheck()) {
                    ++$data['treatment']['value']['data']['securityUpdate']['yes'];
                } else {
                    ++$data['treatment']['value']['data']['securityUpdate']['no'];
                }
            }
        }

        // VIOLATION
        $data['violation']['value']['all'] = \count($violations);

        // CONFORMITE TRAITEMENT
        if ($collectivity->isHasModuleConformiteTraitement()) {
            foreach (ConformiteTraitementLevelDictionary::getConformites() as $key => $label) {
                $data['conformiteTraitement']['data'][$key] = 0;
                $data['conformiteTraitement']['labels'][]   = $label;
                $data['conformiteTraitement']['colors'][]   = ConformiteTraitementLevelDictionary::getRgbConformitesColorsForChartView()[$key];
            }

            $conformiteTraitements                                                                 = $this->conformiteTraitementRepository->findActiveByCollectivity($collectivity);
            $nbTreatmentWithNoConformiteTraitements                                                = $this->treatmentRepository->countAllWithNoConformiteTraitementByCollectivity($collectivity);
            $data['conformiteTraitement']['data'][ConformiteTraitementLevelDictionary::NON_EVALUE] = $nbTreatmentWithNoConformiteTraitements;

            /** @var Model\ConformiteTraitement\ConformiteTraitement $conformiteTraitement */
            foreach ($conformiteTraitements as $conformiteTraitement) {
                $level = ConformiteTraitementCompletion::getConformiteTraitementLevel($conformiteTraitement);
                ++$data['conformiteTraitement']['data'][$level];

                if (
                    $conformiteTraitement->getNeedsAipd()
                ) {
                    ++$data['aipd']['toDo'];
                }
            }

            // reset data if all values equal zéro. Need to hide the chart.
            if (empty(array_filter($data['conformiteTraitement']['data']))) {
                $data['conformiteTraitement']['data'] = [];
            } else {
                $data['conformiteTraitement']['data'] = \array_values($data['conformiteTraitement']['data']);
            }
        }

        // CONFORMITE ORGANISATION
        if ($collectivity->isHasModuleConformiteOrganisation() && null !== $conformiteOrganisationEvaluation) {
            $conformites = ConformiteOrganisationService::getOrderedConformites($conformiteOrganisationEvaluation);

            foreach ($conformites as $conformite) {
                $data['conformiteOrganisation'][$conformite->getProcessus()->getPosition()]['processus']  = $conformite->getProcessus()->getNom();
                $data['conformiteOrganisation'][$conformite->getProcessus()->getPosition()]['conformite'] = $conformite->getConformite();
            }
        }

        return $data;
    }

    public function getTemplateViewName(): string
    {
        return 'Reporting/Dashboard/index.html.twig';
    }
}
