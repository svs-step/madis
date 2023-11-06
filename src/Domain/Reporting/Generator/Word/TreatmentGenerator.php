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

namespace App\Domain\Reporting\Generator\Word;

use App\Domain\Registry\Dictionary\TreatmentAuthorDictionary;
use App\Domain\Registry\Dictionary\TreatmentCollectingMethodDictionary;
use App\Domain\Registry\Dictionary\TreatmentLegalBasisDictionary;
use App\Domain\Registry\Dictionary\TreatmentUltimateFateDictionary;
use App\Domain\Registry\Model\Treatment;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Shared\Converter;

class TreatmentGenerator extends AbstractGenerator implements ImpressionGeneratorInterface
{
    /**
     * Global overview : Information to display for treatment in overview report.
     */
    public function addGlobalOverview(Section $section, array $data): void
    {
        // GENERATE ALL DATA BEFORE WORD GENERATION IN ORDER TO AVOID SEVERAL LOOP
        $nbTreatments = \count($data);
        $overview     = [
            [
                'Nom',
                'Gestionnaire',
            ],
        ];
        $digitalisation = [
            'paper'       => 0,
            'onlyDigital' => 0,
            'digital'     => 0,
            'both'        => 0,
            'other'       => 0,
        ];
        $security = [
            'accessControl' => 0,
            'tracability'   => 0,
            'saving'        => 0,
            'update'        => 0,
        ];

        /*
         * @var Treatment
         */
        foreach ($data as $key => $treatment) {
            /* @var Treatment $treatment */
            // Overview

            if (10 > $key) {
                $overview[] = [
                    $treatment->getName(),
                    $treatment->getManager() ?? $this->parameterBag->get('APP_DEFAULT_REFERENT'),
                ];
            }

            // Digitalisation
            $hasSoft = $treatment->getCollectivity()->isHasModuleTools() ? count($treatment->getTools()) > 0 : !\is_null($treatment->getSoftware());
            if ($hasSoft && $treatment->isPaperProcessing()) {
                ++$digitalisation['both'];
            } elseif ($hasSoft) {
                ++$digitalisation['onlyDigital'];
            } elseif ($treatment->isPaperProcessing()) {
                ++$digitalisation['paper'];
            } else {
                ++$digitalisation['other'];
            }

            // Security
            if ($hasSoft) {
                if ($treatment->getSecurityAccessControl()->isCheck()) {
                    ++$security['accessControl'];
                }
                if ($treatment->getSecurityTracability()->isCheck()) {
                    ++$security['tracability'];
                }
                if ($treatment->getSecuritySaving()->isCheck()) {
                    ++$security['saving'];
                }
                if ($treatment->getSecurityUpdate()->isCheck()) {
                    ++$security['update'];
                }
            }
        }
        // Then aggregate
        $digitalisation['digital'] = $digitalisation['onlyDigital'] + $digitalisation['both'];

        $section->addTitle('Registre des traitements', 2);
        $section->addText('Une collecte des traitements a été réalisée. Chaque fiche de registre est établie sur une base de 20 critères. Les critères exigés par le règlement sont pris en compte.');

        $textrun = $section->addTextRun();
        $textrun->addText('Une version de ces traitements et à valeur de preuve ');
        $textrun->addLink('listTreatments', 'figure en annexe.', ['underline' => 'single'], [], true);

        $section->addTitle('Analyse du registre des traitements', 2);
        $section->addText("Il y a aujourd’hui {$nbTreatments} traitements de données à caractère personnel inventoriés");

        $section->addText('Informatisation des traitements :');

        $categories = ['Uniquement papier', 'Complétement informatisé', 'Informatisé et papier', 'Non renseigné'];
        $chartData  = $digitalisation;
        unset($chartData['digital']); // Remove aggregate data which cumulate onlyDigital + both

        $chartTable = $section->addTable();
        $chartTable->addRow();
        $cell = $chartTable->addCell(4000);
        $cell->addChart(
            'pie',
            $categories,
            $chartData,
            [
                'height'        => Converter::cmToEmu(6),
                'width'         => Converter::cmToEmu(9),
                'alignmant'     => 'left',
                'wrappingStyle' => 'square',
                'positioning'   => 'absolute',
            ]
        );
        $cell = $chartTable->addCell(4000);
        $cell->addText("Sur les {$nbTreatments} traitements : ");
        $cell->addListItem("{$digitalisation['paper']} sont uniquement papier");
        $cell->addListItem("{$digitalisation['onlyDigital']} sont complétement informatisés");
        $cell->addListItem("{$digitalisation['both']} sont informatisés et papier");
        if (0 < $digitalisation['other']) {
            $cell->addListItem("{$digitalisation['other']} ne sont pas renseignés");
        }

        $section->addTitle('Sécurité de base des traitements informatisés', 2);
        $section->addText("Sur les {$digitalisation['digital']} traitements informatisés :");
        $section->addListItem("{$security['accessControl']} ont un contrôle d'accès");
        $section->addListItem("{$security['tracability']} ont une traçabilité");
        $section->addListItem("{$security['saving']} sont sauvegardés");
        $section->addListItem("{$security['update']} sont mis à jour");

        $section->addText('Par ailleurs des mesures de protection ont été mises en place sur les ' . ($nbTreatments - $digitalisation['digital']) . ' traitements non informatisés.');
    }

    public function addSyntheticView(Section $section, array $data, bool $forOverviewReport = false): void
    {
        // Break page for overview report
        if ($forOverviewReport) {
            $section->addPageBreak();
        }

        $section->addTitle('Liste des traitements', $forOverviewReport ? 2 : 1);

        // Table data
        // Add header
        $tableData = [
            [
                'Nom',
                'Gestionnaire',
            ],
        ];
        /** @var Treatment $treatment */
        // Add content
        foreach ($data as $treatment) {
            $tableData[] = [
                $treatment->getName() . ('draft' === $treatment->getStatut() ? ' (Brouillon)' : ''),
                $treatment->getManager() ?? $this->parameterBag->get('APP_DEFAULT_REFERENT'),
            ];
        }

        $this->addTable($section, $tableData, true, self::TABLE_ORIENTATION_HORIZONTAL);

        // Don't break page if it's overview report
        if (!$forOverviewReport) {
            $section->addPageBreak();
        }
    }

    public function addDetailedView(Section $section, array $data): void
    {
        $section->addTitle('Détail des traitements', 1);

        foreach ($data as $key => $treatment) {
            /* @var Treatment $treatment */
            if (0 !== $key) {
                $section->addPageBreak();
            }

            $section->addTitle($treatment->getName() . ('draft' === $treatment->getStatut() ? ' (Brouillon)' : ''), 2);

            $generalInformationsData = [
                [
                    'Publique',
                    $treatment->getPublic() ? 'Oui' : 'Non',
                ],
                [
                    'Service',
                    $treatment->getService() ? $treatment->getService()->getName() : '',
                ],
                [
                    'Finalités',
                    $treatment->getGoal() ? \preg_split('/\R/', $treatment->getGoal()) : null,
                ],
                [
                    'En tant que',
                    !\is_null($treatment->getAuthor()) && array_key_exists($treatment->getAuthor(), TreatmentAuthorDictionary::getAuthors()) ? TreatmentAuthorDictionary::getAuthors()[$treatment->getAuthor()] : $treatment->getAuthor(),
                ],
                [
                    'Gestionnaire',
                    $treatment->getManager() ?? $this->parameterBag->get('APP_DEFAULT_REFERENT'),
                ],
                [
                    'Statut',
                    $treatment->isActive() ? 'Actif' : 'Inactif',
                ],
                [
                    'Base légale',
                    TreatmentLegalBasisDictionary::getBasis()[$treatment->getLegalBasis()],
                ],
                [
                    'Justification de la base légale',
                    $treatment->getLegalBasisJustification() ? \preg_split('/\R/', $treatment->getLegalBasisJustification()) : null,
                ],
                [
                    'Observations',
                    $treatment->getObservation() ? \preg_split('/\R/', $treatment->getObservation()) : null,
                ],
            ];

            $detailsData = [
                0 => [
                    'Estimation du nombre de personnes concernées',
                    $treatment->getEstimatedConcernedPeople(),
                ],
                1 => [
                    'Logiciels et supports',
                    \is_string($treatment->getToolsString()) ? $treatment->getToolsString() : null,
                ],
                2 => [
                    'Gestion papier',
                    $treatment->isPaperProcessing() ? 'Oui' : 'Non',
                ],
                3 => [
                    'Délai de conservation',
                    // Defined below
                ],
                4 => [
                    'Sort final',
                    '',
                ],
                5 => [
                    'Origine des données',
                    $treatment->getDataOrigin(),
                ],
                6 => [
                    'Moyens de la collecte des données	',
                    !\is_null($treatment->getCollectingMethod()) ? join(', ', array_map(function ($cm) {
                        return TreatmentCollectingMethodDictionary::getMethods()[$cm];
                    }, $treatment->getCollectingMethod())) : '',
                ],
                7 => [
                    'Mentions légales apposées',
                    $treatment->getLegalMentions() ? 'Oui' : 'Non',
                ],
                8 => [
                    'Consentement demandé',
                    $treatment->getConsentRequest() ? 'Oui' : 'Non',
                ],
                9 => [
                    'Format de la demande du consentement',
                    $treatment->getConsentRequestFormat(),
                ],
            ];

            // "Délai de conservation"
            if (count($treatment->getShelfLifes()) > 0) {
                foreach ($treatment->getShelfLifes() as $delay) {
                    $detailsData[3][1][] = $delay->name . ' - ' . $delay->duration . ' - ' . TreatmentUltimateFateDictionary::getUltimateFates()[$delay->ultimateFate] . "\n";
                }
            } else {
                $detailsData[3][1][] = '';
            }

            $categoryData = [
                [
                    'Catégorie de données',
                    // Values are added below
                ],
                [
                    'Autres catégories',
                    $treatment->getDataCategoryOther() ? \preg_split('/\R/', $treatment->getDataCategoryOther()) : null,
                ],
            ];
            // Add data categories
            $dataCategories = [];
            foreach ($treatment->getDataCategories() as $category) {
                $dataCategories[] = [
                    'text'  => $category->getName(),
                    'style' => [
                        'bold' => $category->isSensible() ? true : false,
                    ],
                ];
            }
            $categoryData[0][] = $dataCategories;

            $goalData = [
                [
                    'Catégorie de destinataires',
                    $treatment->getRecipientCategory() ? \preg_split('/\R/', $treatment->getRecipientCategory()) : null,
                ],
                [
                    'Sous-traitant(s)',
                    \implode(', ', $treatment->getContractors()->toArray()),
                ],
            ];

            $concernedPeopleData = [
                [
                    'Particuliers',
                    $treatment->getConcernedPeopleParticular()->isCheck() ? 'Oui' : 'Non',
                    $treatment->getConcernedPeopleParticular()->getComment(),
                ],
                [
                    'Internautes',
                    $treatment->getConcernedPeopleUser()->isCheck() ? 'Oui' : 'Non',
                    $treatment->getConcernedPeopleUser()->getComment(),
                ],
                [
                    'Salariés',
                    $treatment->getConcernedPeopleAgent()->isCheck() ? 'Oui' : 'Non',
                    $treatment->getConcernedPeopleAgent()->getComment(),
                ],
                [
                    'Élus',
                    $treatment->getConcernedPeopleElected()->isCheck() ? 'Oui' : 'Non',
                    $treatment->getConcernedPeopleElected()->getComment(),
                ],
                [
                    'Professionnels',
                    $treatment->getConcernedPeopleCompany()->isCheck() ? 'Oui' : 'Non',
                    $treatment->getConcernedPeopleCompany()->getComment(),
                ],
                [
                    'Partenaires',
                    $treatment->getConcernedPeoplePartner()->isCheck() ? 'Oui' : 'Non',
                    $treatment->getConcernedPeoplePartner()->getComment(),
                ],
                [
                    'Autres',
                    $treatment->getConcernedPeopleOther()->isCheck() ? 'Oui' : 'Non',
                    $treatment->getConcernedPeopleOther()->getComment(),
                ],
            ];

            $securityData = [
                [
                    'Contrôle d\'accès',
                    $treatment->getSecurityAccessControl()->isCheck() ? 'Oui' : 'Non',
                    $treatment->getSecurityAccessControl()->getComment(),
                ],
                [
                    'Traçabilité',
                    $treatment->getSecurityTracability()->isCheck() ? 'Oui' : 'Non',
                    $treatment->getSecurityTracability()->getComment(),
                ],
                [
                    'Sauvegarde',
                    $treatment->getSecuritySaving()->isCheck() ? 'Oui' : 'Non',
                    $treatment->getSecuritySaving()->getComment(),
                ],
                [
                    'Mise à jour',
                    $treatment->getSecurityUpdate()->isCheck() ? 'Oui' : 'Non',
                    $treatment->getSecurityUpdate()->getComment(),
                ],
                [
                    'Autres',
                    $treatment->getSecurityOther()->isCheck() ? 'Oui' : 'Non',
                    $treatment->getSecurityOther()->getComment(),
                ],
                [
                    'Je suis en capacité de ressortir les personnes habilitées',
                    $treatment->isSecurityEntitledPersons() ? 'Oui' : 'Non',
                    '',
                ],
                [
                    'La personne ou la procédure qui permet d’ouvrir des comptes est clairement identifiée	',
                    $treatment->isSecurityOpenAccounts() ? 'Oui' : 'Non',
                    '',
                ],
                [
                    'Les spécificités de sensibilisation liées à ce traitement sont délivrées	',
                    $treatment->isSecuritySpecificitiesDelivered() ? 'Oui' : 'Non',
                    '',
                ],
            ];

            $specificData = [
                [
                    'Surveillance systématique',
                    $treatment->isSystematicMonitoring() ? 'Oui' : 'Non',
                ],
                [
                    'Collecte à large échelle',
                    $treatment->isLargeScaleCollection() ? 'Oui' : 'Non',
                ],
                [
                    'Personnes vulnérables',
                    $treatment->isVulnerablePeople() ? 'Oui' : 'Non',
                ],
                [
                    'Croisement de données',
                    $treatment->isDataCrossing() ? 'Oui' : 'Non',
                ],
                [
                    'Évaluation ou notation',
                    $treatment->isEvaluationOrRating() ? 'Oui' : 'Non',
                ],
                [
                    'Décisions automatisées  avec  effet  juridique',
                    $treatment->isAutomatedDecisionsWithLegalEffect() ? 'Oui' : 'Non',
                ],
                [
                    'Exclusion automatique d\'un service',
                    $treatment->isAutomaticExclusionService() ? 'Oui' : 'Non',
                ],
            ];

            $historyData = [
                [
                    'Date de création',
                    $this->getDate($treatment->getCreatedAt()),
                ],
                [
                    'Date de modification',
                    $this->getDate($treatment->getUpdatedAt()),
                ],
                [
                    'Modifié par',
                    $treatment->getUpdatedBy(),
                ],
            ];

            $section->addTitle('Informations générales', 3);
            $this->addTable($section, $generalInformationsData, false, self::TABLE_ORIENTATION_VERTICAL);

            $section->addTitle('Détails - Personnes concernées', 3);
            $this->addTable($section, $concernedPeopleData, false, self::TABLE_ORIENTATION_VERTICAL);

            $section->addTitle('Détails', 3);
            $this->addTable($section, $detailsData, false, self::TABLE_ORIENTATION_VERTICAL);

            $section->addTitle('Catégorie de données', 3);
            $this->addTable($section, $categoryData, false, self::TABLE_ORIENTATION_VERTICAL);

            $section->addTitle('Destination', 3);
            $this->addTable($section, $goalData, false, self::TABLE_ORIENTATION_VERTICAL);

            $section->addTitle('Mesures de sécurité et confidentialité', 3);
            $this->addTable($section, $securityData, false, self::TABLE_ORIENTATION_VERTICAL);

            $section->addTitle('Traitement spécifique', 3);
            $this->addTable($section, $specificData, false, self::TABLE_ORIENTATION_VERTICAL);

            $section->addTitle('Historique', 3);
            $this->addTable($section, $historyData, false, self::TABLE_ORIENTATION_VERTICAL);
        }
    }

    public function TreatmentAnnexeList($section, $data)
    {
        $section->addBookmark('listTreatments');
        $section->addTitle('Liste des traitements', 2);
        $treatmentAnnexListTable = $section->addTable($this->tableStyle);
        $treatmentAnnexListTable->addRow(null, ['tblHeader' => true, 'cantsplit' => true]);
        $cell = $treatmentAnnexListTable->addCell(3000, $this->cellHeadStyle);
        $cell->addText('Nom', $this->textHeadStyle);
        $cell = $treatmentAnnexListTable->addCell(2000, $this->cellHeadStyle);
        $cell->addText('Gestionnaire', $this->textHeadStyle);
        $cell = $treatmentAnnexListTable->addCell(1500, $this->cellHeadStyle);
        $cell->addText('Support', $this->textHeadStyle);
        $cell = $treatmentAnnexListTable->addCell(2000, $this->cellHeadStyle);
        $cell->addText('Mesures de sécurité et confidentialité', $this->textHeadStyle);

        foreach ($data as $item) {
            $treatmentAnnexListTable->addRow(null, ['cantsplit' => true]);
            $cell = $treatmentAnnexListTable->addCell(3000);
            $cell->addText($item->getName());
            $cell = $treatmentAnnexListTable->addCell(2000);
            $cell->addText($item->getManager());
            $cell    = $treatmentAnnexListTable->addCell(1500);
            $support = $item->isPaperProcessing() ? 'Papier, ' : '';
            $support = $item->getSoftware() ? $support . $item->getSoftware() : rtrim($support, ', ');

            $cell->addText($support);
            $cell = $treatmentAnnexListTable->addCell(2000);
            if ($item->getSecurityAccessControl()->isCheck()) {
                $cell->addListItem('Contrôle d\'accès', null, [], [], ['spaceAfter' => 0]);
            }
            if ($item->getSecurityTracability()->isCheck()) {
                $cell->addListItem('Traçabilité', null, [], [], ['spaceAfter' => 0]);
            }
            if ($item->getSecuritySaving()->isCheck()) {
                $cell->addListItem('Sauvegarde', null, [], [], ['spaceAfter' => 0]);
            }
            if ($item->getSecurityUpdate()->isCheck()) {
                $cell->addListItem('Mise à jour', null, [], [], ['spaceAfter' => 0]);
            }
        }
    }

    public function RiskTreatmentAnnexeList($riskAipdSection, $treatments)
    {
        $riskAipdSection->addBookmark('AipdRisks');
        $riskAipdSection->addTitle('Liste des traitements à risques', 2);
        $RiskTreatmentAnnexListTable = $riskAipdSection->addTable($this->tableStyle);
        $RiskTreatmentAnnexListTable->addRow(3000, ['tblHeader' => true, 'cantsplit' => true]);

        $CellsStyle = [
            'bgColor' => '3c8dbc',
            'vAlign'  => 'bottom',
        ];

        $cell = $RiskTreatmentAnnexListTable->addCell(3000, $CellsStyle);
        $cell->addText('Traitements', $this->textHeadStyle);
        $cell = $RiskTreatmentAnnexListTable->addCell(2000, $CellsStyle);
        $cell->addText('Type de données', $this->textHeadStyle);

        $titles = ['Données sensibles',
            'Surveillance systématique',
            'Collecte à large échelle',
            'Personnes vulnérables',
            'Croisement de données',
            'Évaluation ou notation',
            'Décisions automatisées avec effet',
            'Exclusion automatique d\'un service',
            'Usage innovant',
            'CNIL AIPD exemptée',
            '',
            'AIPD requise'];

        $verticalStyle = [
            'bgColor'       => '3c8dbc',
            'textDirection' => \PhpOffice\PhpWord\Style\Cell::TEXT_DIR_BTLR,
            'vAlign'        => 'center',
        ];
        foreach ($titles as $title) {
            if ('' === $title) {
                $cell = $RiskTreatmentAnnexListTable->addCell(10, ['borderTopColor' => 'ffffff', 'borderTopSize' => 2, 'borderBottomColor' => 'ffffff', 'borderBottomSize' => 2]);
                $cell->addText($title);
            } else {
                $cell = $RiskTreatmentAnnexListTable->addCell(50, $verticalStyle);
                $cell->addText($title, $this->textHeadStyle);
            }
        }

        foreach ($treatments as $item) {
            $cnt_sensible                                                        = 0;
            $sensibleDatas                                                       = [];
            $specificTreatments                                                  = [];
            $item->isSystematicMonitoring() ? $specificTreatments[]              = 'Surveillance systématique' : null;
            $item->isLargeScaleCollection() ? $specificTreatments[]              = 'Collecte à large échelle' : null;
            $item->isVulnerablePeople() ? $specificTreatments[]                  = 'Personnes vulnérables' : null;
            $item->isDataCrossing() ? $specificTreatments[]                      = 'Croisement de données' : null;
            $item->isEvaluationOrRating() ? $specificTreatments[]                = 'Évaluation ou notation' : null;
            $item->isAutomatedDecisionsWithLegalEffect() ? $specificTreatments[] = 'Décisions automatisées avec effet' : null;
            $item->isAutomaticExclusionService() ? $specificTreatments[]         = "Exclusion automatique d'un service" : null;
            $item->isInnovativeUse() ? $specificTreatments[]                     = 'Usage innovant' : null;

            $cnt_categories = count(array_filter($specificTreatments));

            // Ignore treatments with no sensitive specific treatments.
            // https://gitlab.adullact.net/soluris/madis/-/issues/859
            if (0 === $cnt_categories) {
                continue;
            }

            foreach ($item->getDataCategories() as $category) {
                if ($category->isSensible()) {
                    ++$cnt_sensible;
                }
            }

            $RiskTreatmentAnnexListTable->addRow(null, ['cantsplit' => true]);
            $cell = $RiskTreatmentAnnexListTable->addCell();
            $cell->addText($item->getName(), ['size' => 8]);
            $cell = $RiskTreatmentAnnexListTable->addCell();
            foreach ($item->getDataCategories() as $category) {
                $cell->addListItem($category->getName(), null, ['size' => 8], [], ['spaceAfter' => 0]);
            }
            $cell = $RiskTreatmentAnnexListTable->addCell(null, ['bgColor' => $cnt_sensible > 0 ? 'ffa7a7' : null, 'valign' => 'center']);
            $cell->addText($cnt_sensible > 0 ? 'Oui' : '', ['size' => 8, 'bold' => true], ['alignment' => 'center']);
            $cell = $RiskTreatmentAnnexListTable->addCell(null, ['bgColor' => $item->isSystematicMonitoring() ? 'ffa7a7' : null, 'valign' => 'center']);
            $cell->addText($item->isSystematicMonitoring() ? 'Oui' : '', ['size' => 8, 'bold' => true], ['alignment' => 'center']);
            $cell = $RiskTreatmentAnnexListTable->addCell(null, ['bgColor' => $item->isLargeScaleCollection() ? 'ffa7a7' : null, 'valign' => 'center']);
            $cell->addText($item->isLargeScaleCollection() ? 'Oui' : '', ['size' => 8, 'bold' => true], ['alignment' => 'center']);
            $cell = $RiskTreatmentAnnexListTable->addCell(null, ['bgColor' => $item->isVulnerablePeople() ? 'ffa7a7' : null, 'valign' => 'center']);
            $cell->addText($item->isVulnerablePeople() ? 'Oui' : '', ['size' => 8, 'bold' => true], ['alignment' => 'center']);
            $cell = $RiskTreatmentAnnexListTable->addCell(null, ['bgColor' => $item->isDataCrossing() ? 'ffa7a7' : null, 'valign' => 'center']);
            $cell->addText($item->isDataCrossing() ? 'Oui' : '', ['size' => 8, 'bold' => true], ['alignment' => 'center']);
            $cell = $RiskTreatmentAnnexListTable->addCell(null, ['bgColor' => $item->isEvaluationOrRating() ? 'ffa7a7' : null, 'valign' => 'center']);
            $cell->addText($item->isEvaluationOrRating() ? 'Oui' : '', ['size' => 8, 'bold' => true], ['alignment' => 'center']);
            $cell = $RiskTreatmentAnnexListTable->addCell(null, ['bgColor' => $item->isAutomatedDecisionsWithLegalEffect() ? 'ffa7a7' : null, 'valign' => 'center']);
            $cell->addText($item->isAutomatedDecisionsWithLegalEffect() ? 'Oui' : '', ['size' => 8, 'bold' => true], ['alignment' => 'center']);
            $cell = $RiskTreatmentAnnexListTable->addCell(null, ['bgColor' => $item->isAutomaticExclusionService() ? 'ffa7a7' : null, 'valign' => 'center']);
            $cell->addText($item->isAutomaticExclusionService() ? 'Oui' : '', ['size' => 8, 'bold' => true], ['alignment' => 'center']);
            $cell = $RiskTreatmentAnnexListTable->addCell(null, ['bgColor' => $item->isInnovativeUse() ? 'ffa7a7' : null, 'valign' => 'center']);
            $cell->addText($item->isInnovativeUse() ? 'Oui' : '', ['size' => 8, 'bold' => true], ['alignment' => 'center']);
            $cell = $RiskTreatmentAnnexListTable->addCell(null, ['bgColor' => $item->isExemptAIPD() ? 'bce292' : 'ffa7a7', 'valign' => 'center']);
            $cell->addText($item->isExemptAIPD() ? 'Oui' : 'Non', ['size' => 8, 'bold' => true], ['alignment' => 'center']);
            $cell = $RiskTreatmentAnnexListTable->addCell(10, ['borderTopColor' => 'ffffff', 'borderTopSize' => 2, 'borderBottomColor' => 'ffffff', 'borderBottomSize' => 2]);
            $cell->addText('');
            // https://gitlab.adullact.net/soluris/madis/-/issues/859
            $cell = $RiskTreatmentAnnexListTable->addCell(null, ['bgColor' => ((($cnt_sensible > 0 && $cnt_categories > 0) || $cnt_categories > 1 || ($cnt_sensible > 2)) && false === $item->isExemptAIPD()) ? 'bce292' : 'ffa7a7', 'valign' => 'center']);
            $cell->addText(((($cnt_sensible > 0 && $cnt_categories > 0) || $cnt_categories > 1 || ($cnt_sensible > 2)) && false === $item->isExemptAIPD()) ? 'Oui' : 'Non', ['size' => 8, 'bold' => true], ['alignment' => 'center']);
        }
        $riskAipdSection->addPageBreak();
    }

    private function IsMesureOk($data)
    {
        return $data->isCheck() ? '00a65a' : '000000';
    }
}
