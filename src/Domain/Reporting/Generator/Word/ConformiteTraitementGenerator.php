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
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Shared\Converter;

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

        uasort($data, [$this, 'sortConformiteTraitementByLevel']);

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
        foreach (ConformiteTraitementLevelDictionary::getConformites() as $key => $label) {
            $chartCategories[] = $label;
            $chartData[$key]   = 0;
        }

        /** @var ConformiteTraitement $conformiteTraitement */
        foreach ($data as $conformiteTraitement) {
            $level = ConformiteTraitementCompletion::getConformiteTraitementLevel($conformiteTraitement);

            $tableData[] = [
                $conformiteTraitement->getTraitement()->getName(),
                $conformiteTraitement->getTraitement()->getManager(),
                ConformiteTraitementLevelDictionary::getConformites()[$level],
                $this->getDate($conformiteTraitement->getCreatedAt(), 'd/m/Y'),
            ];

            ++$chartData[$level];
        }

        $chart = $section->addChart(
            'pie',
            $chartCategories,
            $chartData,
            [
                'height' => Converter::cmToEmu(11),
                'width'  => Converter::cmToEmu(15),
            ]
        );

        $chart->getStyle()->setColors(\array_values(ConformiteTraitementLevelDictionary::getConformitesColors()));

        $this->addTable($section, $tableData, true, self::TABLE_ORIENTATION_HORIZONTAL);
    }

    /**
     * {@inheritdoc}
     */
    public function addSyntheticView(Section $section, array $data): void
    {
        $section->addTitle('Liste des traitements', 1);

        uasort($data, [$this, 'sortConformiteTraitementByLevel']);

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

        /** @var ConformiteTraitement $conformiteTraitement */
        foreach ($data as $conformiteTraitement) {
            $level = ConformiteTraitementCompletion::getConformiteTraitementLevel($conformiteTraitement);

            $tableData[] = [
                $conformiteTraitement->getTraitement()->getName(),
                $conformiteTraitement->getTraitement()->getManager(),
                ConformiteTraitementLevelDictionary::getConformites()[$level],
                $this->getDate($conformiteTraitement->getCreatedAt(), 'd/m/Y'),
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

        /** @var ConformiteTraitement $conformiteTraitement */
        foreach ($data as $key => $conformiteTraitement) {
            if (0 != $key) {
                $section->addPageBreak();
            }

            $questionsData = [
                [
                    'data' => [
                        'Questions',
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
                    !empty($actionsProtections) ? \implode(', ', $actionsProtections) : 'Pas d\'action',
                ];
            }

            $section->addTitle($conformiteTraitement->getTraitement()->getName(), 3);
            $this->addTable($section, $questionsData, true, self::TABLE_ORIENTATION_VERTICAL);

            $historyData = [
                [
                    'Créateur',
                    $conformiteTraitement->getCreator(),
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

    private function sortConformiteTraitementByLevel(ConformiteTraitement $a, ConformiteTraitement $b)
    {
        $weightA = ConformiteTraitementLevelDictionary::getConformitesWeight()[ConformiteTraitementCompletion::getConformiteTraitementLevel($a)];
        $weightB = ConformiteTraitementLevelDictionary::getConformitesWeight()[ConformiteTraitementCompletion::getConformiteTraitementLevel($b)];

        if ($weightA === $weightB) {
            return 0;
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
