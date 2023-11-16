<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author ANODE <contact@agence-anode.fr>
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

use App\Domain\Registry\Calculator\Completion\ConformiteTraitementCompletion;
use App\Domain\Registry\Dictionary\ConformiteTraitementLevelDictionary;
use App\Domain\Registry\Model\ConformiteTraitement\Reponse;
use App\Domain\Registry\Model\Mesurement;
use App\Domain\Registry\Model\Treatment;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Style\ListItem;

class ConformiteTraitementGenerator extends AbstractGenerator implements ImpressionGeneratorInterface
{
    /**
     * Global overview : Information to display for conformiteTraitement in overview report.
     */
    public function addGlobalOverview(Section $section, array $data): void
    {
        if (empty($data)) {
            return;
        }

        $section->addTitle('Analyse de la conformité des traitements', 2);

        uasort($data, [$this, 'sortTreatmentByConformiteTraitementByLevelAndTreatmentName']);

        // Table data
        // Add header
        $tableData = [
            [
                'Traitement',
                'Gestionnaire',
                'Date de révision de la conformité',
                'Conformité',
            ],
        ];

        $chartCategories = [];
        $chartData       = [];
        $conformites     = ConformiteTraitementLevelDictionary::getConformites();
        foreach ($conformites as $key => $label) {
            $chartCategories[] = $label;
            $chartData[$key]   = 0;
        }

        /** @var Treatment $treatment */
        foreach ($data as $treatment) {
            $conformiteTraitement = $treatment->getConformiteTraitement();
            $level                = ConformiteTraitementCompletion::getConformiteTraitementLevel($conformiteTraitement);

            $date = null;
            if (!\is_null($conformiteTraitement)) {
                $date = $conformiteTraitement->getCreatedAt();
            }

            $tableData[] = [
                $treatment->getName(),
                $treatment->getManager(),
                ConformiteTraitementLevelDictionary::getConformites()[$level],
                $this->getDate($date, 'd/m/Y'),
            ];

            ++$chartData[$level];
        }

        $section->addText('Les 10 critères suivants correspondent aux principes fondamentaux du RGPD et ont fait l’objet d’une évaluation :');
        $listStyle = ['listType' => ListItem::TYPE_NUMBER];
        $section->addListItem('Finalités', 0, null, $listStyle);
        $section->addListItem('Licéité', 0, null, $listStyle);
        $section->addListItem('Minimisation des données', 0, null, $listStyle);
        $section->addListItem('Qualité des données', 0, null, $listStyle);
        $section->addListItem('Durée de conservation', 0, null, $listStyle);
        $section->addListItem('Information des personnes concernées', 0, null, $listStyle);
        $section->addListItem('Recueil de consentement', 0, null, $listStyle);
        $section->addListItem('Exercice des différents droits', 0, null, $listStyle);
        $section->addListItem('Sous-traitance', 0, null, $listStyle);
        $section->addListItem('Transferts en dehors de l’union européenne', 0, null, $listStyle);

        $textrun = $section->addTextRun();
        $textrun->addText('Une synthèse de l’analyse de la conformité des traitements et à valeur de preuve ');
        $textrun->addLink('listConformityTreatments', 'figure en annexe', ['underline' => 'single'], [], true);
        $textrun->addText('.');

        $section->addText('Ci-dessous l’évaluation de la conformité des traitements au ' . date('d/m/Y') . ' :');

        $chart = $section->addChart(
            'pie',
            $chartCategories,
            $chartData,
            [
                'height' => Converter::cmToEmu(11),
                'width'  => Converter::cmToEmu(15),
            ]
        );

        $chart->getStyle()->setColors(\array_values(ConformiteTraitementLevelDictionary::getHexaConformitesColors()));

        $countTypes          = array_count_values(array_column($tableData, '2'));
        $conformes           = array_key_exists('Conforme', $countTypes) ? $countTypes['Conforme'] : 0;
        $nonConformesMineurs = array_key_exists('Non-conformité mineure', $countTypes) ? $countTypes['Non-conformité mineure'] : 0;
        $nonConformesMajeurs = array_key_exists('Non-conformité majeure', $countTypes) ? $countTypes['Non-conformité majeure'] : 0;
        $NonEvalues          = array_key_exists('Non évalué', $countTypes) ? $countTypes['Non évalué'] : 0;

        $section->addText('Sur les ' . (count($tableData) - 1) . ' traitements :');
        $section->addListItem('Conformes : ' . $conformes);
        $section->addListItem('Non-conformité mineure : ' . $nonConformesMineurs);
        $section->addListItem('Non-conformité majeure : ' . $nonConformesMajeurs);
        $section->addListItem('Non évalué : ' . $NonEvalues);

        $tableStyleConformite = [
            'borderColor' => '006699',
            'borderSize'  => 6,
            'cellMargin'  => 100,
            'unit'        => TblWidth::PERCENT,
            'width'       => 100 * 50,
        ];

        $tableConformite = $section->addTable($tableStyleConformite);
        $headersTable    = $tableData[0];
        $tableConformite->addRow(null, ['tblHeader' => true, 'cantsplit' => true]);
        foreach ($headersTable as $element) {
            $cell = $tableConformite->addCell(2500, $this->cellHeadStyle);
            $cell->addText($element, $this->textHeadStyle);
        }
        unset($tableData[0]);
        foreach ($tableData as $line) {
            $tableConformite->addRow(null, ['cantsplit' => true]);
            $cell1 = $tableConformite->addCell(2500);
            $cell1->addText($line[0]);
            $cell2 = $tableConformite->addCell(2500);
            $cell2->addText($line[1]);
            $cell3 = $tableConformite->addCell(2500);
            $cell3->addText($line[3]);
            $styleCellConformite = match ($line[2]) {
                'Conforme'               => ['bgColor' => 'bce292'],
                'Non-conformité mineure' => ['bgColor' => 'fac9ad'],
                'Non-conformité majeure' => ['bgColor' => 'ffa7a7'],
                'Non évalué'             => ['bgColor' => 'ffffff'],
            };
            $cell4 = $tableConformite->addCell(2500, $styleCellConformite);
            $cell4->addText($line[2], ['bold' => true]);
        }

        // Analyse impact
        $section->addTitle('Analyse d’impact', 2);

        $cntAipdToDo     = 0;
        $cntAipdRealised = 0;
        foreach ($data as $treatment) {
            $conformite = $treatment->getConformiteTraitement();
            $aipd       = null;
            if ($conformite) {
                $aipd = $conformite->getLastAnalyseImpact();
            }

            if ($conformite && $conformite->getNeedsAipd()) {
                ++$cntAipdToDo;
            }

            if ($aipd) {
                ++$cntAipdRealised;
            }
        }

        $section->addText("Une analyse d’impact sur la protection des données est une étude, qui doit être réalisée si possible en amont du projet, sur des traitements contenant des critères susceptibles d'engendrer un risque élevé pour les droits et libertés des personnes concernées. ");
        if (0 === $cntAipdRealised) {
            $section->addText('À ce jour, il n’y a pas eu d’Analyse d’impact réalisées.');
        }

        $textrun = $section->addTextRun();
        $textrun->addText('Le tableau des traitements à risque et à valeur de preuve ');
        $textrun->addLink('AipdRisks', 'figure en annexe.', ['underline' => 'single'], [], true);

        $section->addText('Ci-dessous, la liste des traitements pour lequel une analyse d’impact sur la protection des données est requise. Au vu des critères, il semble que ' . $cntAipdToDo . ' traitements requière(nt) une analyse d’impact');

        $tableNeedAipd = $section->addTable($tableStyleConformite);
        $tableNeedAipd->addRow(null, ['tblHeader' => true, 'cantsplit' => true]);
        foreach (['Nom du traitement', 'Données sensibles', 'Traitement spécifique'] as $element) {
            $cell = $tableNeedAipd->addCell(2500, $this->cellHeadStyle);
            $cell->addText($element, $this->textHeadStyle);
        }

        $aipdFinished = [];
        foreach ($data as $treatment) {
            $cnt_sensible                                                             = 0;
            $sensibleDatas                                                            = [];
            $specificTreatments                                                       = [];
            $treatment->isSystematicMonitoring() ? $specificTreatments[]              = 'Surveillance systématique' : null;
            $treatment->isLargeScaleCollection() ? $specificTreatments[]              = 'Collecte à large échelle' : null;
            $treatment->isVulnerablePeople() ? $specificTreatments[]                  = 'Personnes vulnérables' : null;
            $treatment->isDataCrossing() ? $specificTreatments[]                      = 'Croisement de données' : null;
            $treatment->isEvaluationOrRating() ? $specificTreatments[]                = 'Évaluation ou notation' : null;
            $treatment->isAutomatedDecisionsWithLegalEffect() ? $specificTreatments[] = 'Décisions automatisées avec effet' : null;
            $treatment->isAutomaticExclusionService() ? $specificTreatments[]         = "Exclusion automatique d'un service" : null;
            $treatment->isInnovativeUse() ? $specificTreatments[]                     = 'Usage innovant' : null;

            foreach ($treatment->getDataCategories() as $category) {
                if ($category->isSensible()) {
                    $sensibleDatas[] = $category;
                }
            }
            if ($treatment->getConformiteTraitement() && $treatment->getConformiteTraitement()->getNeedsAipd()) {
                $tableNeedAipd->addRow(null, ['cantsplit' => true]);
                $cell = $tableNeedAipd->addCell(2500);
                $cell->addText($treatment->getName());
                $cell = $tableNeedAipd->addCell(2500);
                foreach ($sensibleDatas as $sensibleData) {
                    $cell->addListItem(htmlspecialchars((string) $sensibleData, ENT_COMPAT, 'UTF-8'), (int) null, [], [], ['spaceAfter' => 0]);
                }
                $cell = $tableNeedAipd->addCell(2500);
                foreach ($specificTreatments as $specificTreatment) {
                    $cell->addListItem($specificTreatment, (int) null, [], [], ['spaceAfter' => 0]);
                }
            }

            if ($treatment->getConformiteTraitement() && $aipd = $treatment->getConformiteTraitement()->getLastAnalyseImpact()) {
                if ('non_realisee' !== $aipd->getStatut() && 'en_cours' !== $aipd->getStatut()) {
                    $aipdFinished[] = [
                        $treatment->getName(),
                        $treatment->getConformiteTraitement()->getLastAnalyseImpact()->getUpdatedAt(),
                        $treatment->getConformiteTraitement()->getLastAnalyseImpact()->getAvisReferent(),
                        $treatment->getConformiteTraitement()->getLastAnalyseImpact()->getAvisDpd(),
                        $treatment->getConformiteTraitement()->getLastAnalyseImpact()->getAvisRepresentant(),
                        $treatment->getConformiteTraitement()->getLastAnalyseImpact()->getAvisResponsable(),
                    ];
                }
            }
        }

        $section->addTextBreak();
        $text = match (count($aipdFinished)) {
            0       => "Aucun traitement n'a fait l'objet d'une analyse d'impact.",
            1       => "1 traitement a fait l'objet d'une analyse d'impact.",
            default => count($aipdFinished) . ' traitements ont fait l’objet d’une analyse d’impact.'
        };
        $section->addText($text);

        $tableAipdExist = $section->addTable($tableStyleConformite);
        $tableAipdExist->addRow(null, ['tblHeader' => true, 'cantSplit' => true]);
        foreach (['Traitements', 'Date de réalisation de l’AIPD', 'Avis du référent RGPD', 'Avis du DPD', 'Avis des représentants des personnes concernées', 'Validation du responsable du traitement'] as $element) {
            $cell = $tableAipdExist->addCell(1000, $this->cellHeadStyle);
            $cell->addText($element, $this->textHeadStyle);
        }
        foreach ($aipdFinished as $line) {
            $tableAipdExist->addRow(null, ['cantSplit' => true]);
            $cell = $tableAipdExist->addCell(1000);
            $cell->addText($line[0]);
            $cell = $tableAipdExist->addCell(1000);
            $cell->addText($line[1]->format('d/m/Y'));
            for ($i = 2; $i <= 5; ++$i) {
                $cell = $tableAipdExist->addCell(1000, ['bgColor' => $this->colorCell($line[$i]->getReponse())]);
                $cell->addText($this->valueCell($line[$i]->getReponse()));
            }
        }
    }

    private function colorCell($value)
    {
        $return_value = match ($value) {
            'favorable'         => 'bce292',
            'defavorable'       => 'ffa7a7',
            'pas_de_reponse'    => 'ffffff',
            'favorable_reserve' => 'fac9ad',
        };

        return $return_value;
    }

    private function valueCell($value)
    {
        $return_value = match ($value) {
            'favorable'         => 'Favorable',
            'defavorable'       => 'Défavorable',
            'pas_de_reponse'    => 'Pas de reponse',
            'favorable_reserve' => 'Favorable avec réserve',
        };

        return $return_value;
    }

    public function addSyntheticView(Section $section, array $data): void
    {
        $section->addTitle('Liste des traitements', 1);
        $section->addBookmark('Traitements en annexe');

        uasort($data, [$this, 'sortTreatmentByConformiteTraitementByLevelAndTreatmentName']);

        // Table data
        // Add header
        $tableData = [
            [
                'Traitement',
                'Gestionnaire',
                'Date de révision de la conformité',
                'Conformité',
            ],
        ];

        /** @var Treatment $treatment */
        foreach ($data as $treatment) {
            $conformiteTraitement = $treatment->getConformiteTraitement();
            $level                = ConformiteTraitementCompletion::getConformiteTraitementLevel($conformiteTraitement);

            $date = null;
            if (!\is_null($conformiteTraitement)) {
                $date = $conformiteTraitement->getCreatedAt();
            }

            $tableData[] = [
                $treatment->getName(),
                $treatment->getManager(),
                $this->getDate($date, 'd/m/Y'),
                ConformiteTraitementLevelDictionary::getConformites()[$level],
            ];
        }

        $this->addTable($section, $tableData, true, self::TABLE_ORIENTATION_HORIZONTAL);
    }

    public function addDetailedView(Section $section, array $data): void
    {
        $section->addTitle('Détail des traitements', 1);

        /** @var Treatment $treatment */
        foreach ($data as $key => $treatment) {
            $conformiteTraitement = $treatment->getConformiteTraitement();
            if (\is_null($conformiteTraitement)) {
                continue;
            }

            if (0 != $key) {
                $section->addPageBreak();
            }

            $questionsData = [
                [
                    'data' => [
                        'Principes fondamentaux',
                        ['text' => 'Conformité', 'style' => $this->textHeadStyle],
                        ['text' => 'Actions de protections', 'style' => $this->textHeadStyle],
                    ],
                    'style' => [
                        'bgColor' => '3c8dbc',
                        'bold'    => true,
                        'color'   => 'ffffff',
                    ],
                ],
            ];

            $reponses = \iterable_to_array($conformiteTraitement->getReponses());

            uasort($reponses, [$this, 'sortReponseByQuestionPosition']);

            foreach ($reponses as $reponse) {
                $actionsProtections = \array_map(function (Mesurement $mesurement) {
                    return $mesurement->getName();
                }, \iterable_to_array($reponse->getActionProtections()));

                $questionsData[] = [
                    $reponse->getQuestion()->getQuestion(),
                    $reponse->isConforme() ? 'Conforme' : 'Non-conforme',
                    !empty($actionsProtections) ? join(', ', $actionsProtections) : 'Pas d\'action',
                ];
            }

            $section->addTitle($conformiteTraitement->getTraitement()->getName(), 3);
            $this->addTable($section, $questionsData, true, self::TABLE_ORIENTATION_VERTICAL);

            $historyData = [
                [
                    'Date de création',
                    $this->getDate($conformiteTraitement->getCreatedAt()),
                ],
                [
                    'Date de modification',
                    $this->getDate($conformiteTraitement->getUpdatedAt()),
                ],
                [
                    'Modifié par',
                    $conformiteTraitement->getUpdatedBy(),
                ],
            ];

            $section->addTitle('Historique', 3);
            $this->addTable($section, $historyData, true, self::TABLE_ORIENTATION_VERTICAL);
        }
    }

    private function sortTreatmentByConformiteTraitementByLevelAndTreatmentName(Treatment $a, Treatment $b)
    {
        $weightA = ConformiteTraitementLevelDictionary::getConformitesWeight()[ConformiteTraitementCompletion::getConformiteTraitementLevel($a->getConformiteTraitement())];
        $weightB = ConformiteTraitementLevelDictionary::getConformitesWeight()[ConformiteTraitementCompletion::getConformiteTraitementLevel($b->getConformiteTraitement())];

        if ($weightA === $weightB) {
            return strcmp($a->getName(), $b->getName());
        }

        return ($weightA < $weightB) ? -1 : 1;
    }

    private function sortReponseByQuestionPosition(Reponse $a, Reponse $b)
    {
        $orderA = $a->getQuestion()->getPosition();
        $orderB = $b->getQuestion()->getPosition();

        return ($orderA < $orderB) ? -1 : 1;
    }

    public function SyntheticAnnexeList($section, $treatments)
    {
        $section->addBookmark('listConformityTreatments');
        $section->addTitle('Synthèse de la conformité des traitements évalués', 2);
        $section->addText('Légende :');
        $section->AddListItem('C = Conforme');
        $section->AddListItem('NCM = Non conforme majeure');
        $section->AddListItem('NC = Non conforme mineure');

        $styleCellHeader = ['textDirection' => \PhpOffice\PhpWord\Style\Cell::TEXT_DIR_BTLR, 'bgColor' => '3c8dbc', 'vAlign' => 'center'];

        // Affichage du header du tableau
        $tableSyntheticAnnexeList = $section->addTable($this->tableStyle);
        $tableSyntheticAnnexeList->addRow(2000, ['tblHeader' => true, 'cantsplit' => true]);
        $cell = $tableSyntheticAnnexeList->addCell(1000, ['bgColor' => '3c8dbc', 'vAlign' => 'bottom']);
        $cell->addText('Traitements', $this->textHeadStyle);

        $ConformiteNames = ['Finalités', 'Licéité du traitement', 'Minimisation des données', 'Qualité des données', 'Durées de conservation', 'Information des personnes', 'Recueil du consentement', "Droit d'Accès", 'Droit de portabilité', 'Droit de rectification', "Droit d'effacement", 'Droit de limitation', "Droit d'opposition", 'Sous-traitance', 'Transferts hors UE'];
        foreach ($ConformiteNames as $item) {
            $cell = $tableSyntheticAnnexeList->addCell(300, $styleCellHeader);
            $cell->addText($item, $this->textHeadStyle);
        }

        $cell = $tableSyntheticAnnexeList->addCell(100, ['borderTopColor' => 'ffffff', 'borderTopSize' => 2, 'borderBottomColor' => 'ffffff', 'borderBottomSize' => 2]);
        $cell->addText('');
        $cell = $tableSyntheticAnnexeList->addCell(300, ['bgColor' => '3c8dbc', 'vAlign' => 'bottom']);
        $cell->addText('C', $this->textHeadStyle);
        $cell = $tableSyntheticAnnexeList->addCell(300, ['bgColor' => '3c8dbc', 'vAlign' => 'bottom']);
        $cell->addText('NC', $this->textHeadStyle);
        $cell = $tableSyntheticAnnexeList->addCell(300, ['bgColor' => '3c8dbc', 'vAlign' => 'bottom']);
        $cell->addText('NCM', $this->textHeadStyle);

        // End header

        $listConformityName = [
            'C'   => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0, 13 => 0, 14 => 0, 15 => 0],
            'NC'  => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0, 13 => 0, 14 => 0, 15 => 0],
            'NCM' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0, 13 => 0, 14 => 0, 15 => 0],
        ];

        // Affichage des données de chaque conformité de traitement
        foreach ($treatments as $treatment) {
            if ($treatment->getConformiteTraitement()) {
                $ConformityTreatmentValues = [];
                /** @var Reponse $response */
                foreach ($treatment->getConformiteTraitement()->getReponses() as $response) {
                    // get number of planned actions for this response
                    $plannedActions = 0;
                    /** @var Mesurement $actionProtection */
                    foreach ($response->getActionProtections() as $actionProtection) {
                        if (!$actionProtection->getPlanificationDate()) {
                            ++$plannedActions;
                        }
                    }
                    $NonConformityValue                                                 = $plannedActions > 0 ? 'NC' : 'NCM';
                    $ConformityTreatmentValues[$response->getQuestion()->getPosition()] = $response->isConforme() ? 'C' : $NonConformityValue;
                }

                $tableSyntheticAnnexeList->addRow();
                $cell = $tableSyntheticAnnexeList->addCell(1000);
                $cell->addText($treatment->getName(), ['size' => 8]);

                $C   = 0;
                $NC  = 0;
                $NCM = 0;

                foreach ($ConformityTreatmentValues as $key => $value) {
                    // For each question add a cell saying if conforme or not
                    $cell = $tableSyntheticAnnexeList->addCell(300, ['bgColor' => $this->BgColorSyntheticTreatment($value), 'vAlign' => 'center']);
                    $cell->addText($value, ['size' => 8], ['alignment' => 'center']);
                    ++$listConformityName[$value][$key];

                    match ($value) {
                        'C'   => $C++,
                        'NC'  => $NC++,
                        'NCM' => $NCM++,
                    };
                }

                $cell = $tableSyntheticAnnexeList->addCell(100, ['borderTopColor' => 'ffffff', 'borderTopSize' => 2, 'borderBottomColor' => 'ffffff', 'borderBottomSize' => 2]);
                $cell->addText('');
                $cell = $tableSyntheticAnnexeList->addCell(300, ['bgColor' => 'bce292', 'vAlign' => 'center']);
                $cell->addText($C, ['bold' => true], ['alignment' => 'center']);
                $cell = $tableSyntheticAnnexeList->addCell(300, ['bgColor' => 'ffff80', 'vAlign' => 'center']);
                $cell->addText($NC, ['bold' => true], ['alignment' => 'center']);
                $cell = $tableSyntheticAnnexeList->addCell(300, ['bgColor' => 'ffa7a7', 'vAlign' => 'center']);
                $cell->addText($NCM, ['bold' => true], ['alignment' => 'center']);
            }
        }

        $tableSyntheticAnnexeList->addRow(60, ['exactHeight' => true]);
        $cell = $tableSyntheticAnnexeList->addCell(1000, ['borderLeftColor' => 'ffffff', 'borderLeftSize' => 2, 'borderRightColor' => 'ffffff', 'borderRightSize' => 2]);
        $cell->addText('');

        foreach ($listConformityName as $key => $datas) {
            $tableSyntheticAnnexeList->addRow(400, ['exactHeight' => true]);
            $cell = $tableSyntheticAnnexeList->addCell(1000, ['bgColor' => '3c8dbc']);
            $cell->addText($key, $this->textHeadStyle, ['alignment' => 'right']);
            foreach ($datas as $item) {
                $cell = $tableSyntheticAnnexeList->addCell(300, ['bgColor' => $this->BgColorSyntheticTreatment($key)]);
                $cell->addText($item, ['bold' => true], ['alignment' => 'center']);
            }
        }
    }

    private function BgColorSyntheticTreatment($value)
    {
        $return_value = match ($value) {
            'C'   => 'bce292',
            'NCM' => 'ffa7a7',
            'NC'  => 'ffff80',
        };

        return $return_value;
    }
}
