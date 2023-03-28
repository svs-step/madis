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
use App\Domain\Registry\Model\ConformiteTraitement\ConformiteTraitement;
use App\Domain\Registry\Model\ConformiteTraitement\Reponse;
use App\Domain\Registry\Model\Mesurement;
use App\Domain\Registry\Model\Treatment;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\TblWidth;

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
                'Conformité',
                'Date de révision de la conformité',
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

        $section->addText("Les 10 critères suivants correspondent aux principes fondamentaux du RGPD et ont fait l’objet d’une évaluation :");
        $section->addListItem('Finalités',0,[],['listType'=> 7]);
        $section->addListItem('Licéité',0,[],['listType'=> 7]);
        $section->addListItem('Minimisation des données',0,[],['listType'=> 7]);
        $section->addListItem('Qualité des données',0,[],['listType'=> 7]);
        $section->addListItem('Durée de conservation',0,[],['listType'=> 7]);
        $section->addListItem('Information des personnes concernées',0,[],['listType'=> 7]);
        $section->addListItem('Recueil de consentement',0,[],['listType'=> 7]);
        $section->addListItem('Exercice des différents droits',0,[],['listType'=> 7]);
        $section->addListItem('Sous-traitance',0,[],['listType'=> 7]);
        $section->addListItem('Transferts en dehors de l’union européenne',0,[],['listType'=> 7]);

        $textrun = $section->addTextRun();
        $textrun->addText('Une synthèse de l’analyse de la conformité des traitements et à valeur de preuve ');
        $textrun->addLink('Traitements en annexe','figure en annexe',['underline' => 'single'],[], true);
        $textrun->addText(".");

        $section->addText("Ci-dessous l’évaluation de la conformité des traitements au ".date("d/m/Y")." :");

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

        $countTypes = array_count_values(array_column($tableData, '2'));
        $conformes = array_key_exists('Conforme',$countTypes) ? $countTypes['Conforme'] : 0;
        $nonConformesMineurs = array_key_exists('Non-conformité mineure',$countTypes) ? $countTypes['Non-conformité mineure'] : 0;
        $nonConformesMajeurs = array_key_exists('Non-conformité majeure',$countTypes) ? $countTypes['Non-conformité majeure'] : 0;
        $NonEvalues = array_key_exists('Non évalué',$countTypes) ? $countTypes['Non évalué'] : 0;

        $section->addText("Sur les ". count($tableData) - 1 ." traitements :");
        $section->addListItem('Conformes : '. $conformes);
        $section->addListItem('Non-conformité mineure : '. $nonConformesMineurs);
        $section->addListItem('Non-conformité majeure : '. $nonConformesMajeurs);
        $section->addListItem('Non évalué : '. $NonEvalues);

        $tableStyleConformite = [
            'borderColor' => '006699',
            'borderSize'  => 6,
            'cellMargin'  => 100,
            'unit'        => TblWidth::PERCENT,
            'width'       => 100 * 50,
        ];

        $tableConformite = $section->addTable($tableStyleConformite);
        $headersTable = $tableData[0];
        $tableConformite->addRow(null, array('tblHeader' => true));
        foreach ($headersTable as $element){
            $cell = $tableConformite->addCell(2500, $this->cellHeadStyle);
            $cell->addText($element, $this->textHeadStyle);
        }
        unset($tableData[0]);
        foreach ($tableData as $line){
            $tableConformite->addRow();
            $cell1 = $tableConformite->addCell(2500);
            $cell1->addText($line[0]);
            $cell2 = $tableConformite->addCell(2500);
            $cell2->addText($line[1]);
            $cell3 = $tableConformite->addCell(2500);
            $cell3->addText($line[3]);
            $styleCellConformite = match($line[2]){
                'Conforme' => ['bgColor' => 'bce292'],
                'Non-conformité mineure' => ['bgColor' => 'fac9ad' ],
                'Non-conformité majeure' => ['bgColor' => 'ffa7a7' ],
                'Non évalué' => ['bgColor' => 'ffffff'],
            };
            $cell4 = $tableConformite->addCell(2500, $styleCellConformite);
            $cell4->addText($line[2], ['bold' => true]);
        }

        //$this->addTable($section, $tableData, true, self::TABLE_ORIENTATION_HORIZONTAL);
    }

    /**
     * {@inheritdoc}
     */
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
                'Conformité',
                'Date de révision de la conformité',
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
                ConformiteTraitementLevelDictionary::getConformites()[$level],
                $this->getDate($date, 'd/m/Y'),
            ];
        }

        $this->addTable($section, $tableData, true, self::TABLE_ORIENTATION_HORIZONTAL);
    }

    /**
     * {@inheritdoc}
     */
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
                        [['text' => 'Conformité', 'style' => $this->textHeadStyle]],
                        [['text' => 'Actions de protections', 'style' => $this->textHeadStyle]],
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
                    !empty($actionsProtections) ? $actionsProtections : 'Pas d\'action',
                ];
            }

            $section->addTitle($conformiteTraitement->getTraitement()->getName(), 3);
            $this->addTable($section, $questionsData, true, self::TABLE_ORIENTATION_VERTICAL);

            $historyData = [
                [
                    'Créateur',
                    strval($conformiteTraitement->getCreator()),
                ],
                [
                    'Date de création',
                    $this->getDate($conformiteTraitement->getCreatedAt()),
                ],
                [
                    'Dernière mise à jour',
                    $this->getDate($conformiteTraitement->getUpdatedAt()),
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
}
