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

use App\Domain\Maturity\Model\Answer;
use App\Domain\Maturity\Model\OptionalAnswer;
use App\Domain\Maturity\Model\Survey;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\TblWidth;

class MaturityGenerator extends AbstractGenerator implements ImpressionGeneratorInterface
{
    public const RESPONSE_0 = 'Rien (ou presque) n\'est fait';
    public const RESPONSE_1 = 'La pratique est partiellement mise en place';
    public const RESPONSE_2 = 'La pratique est conforme';

    /**
     * Global overview : Information to display for maturity in overview report.
     */
    public function addGlobalOverview(Section $section, array $data): void
    {
        if (empty($data)) {
            $section->addTextBreak(2);
            $section->addText("Aucune évaluation de la mise en conformité n'a pour l'heure été effectuée.", ['italic' => true]);

            return;
        }

        $maturityList = [];
        $domainsName  = [];
        if (isset($data['old'])) {
            foreach ($data['old']->getMaturity() as $maturity) {
                $maturityList[$maturity->getDomain()->getPosition()]['old'] = $maturity->getScore();
                if (!isset($domainsName[$maturity->getDomain()->getPosition()])) {
                    $domainsName[$maturity->getDomain()->getPosition()] = $maturity->getDomain()->getName();
                }
            }
        }
        foreach ($data['new']->getMaturity() as $maturity) {
            $maturityList[$maturity->getDomain()->getPosition()]['new'] = $maturity->getScore();
            if (!isset($domainsName[$maturity->getDomain()->getPosition()])) {
                $domainsName[$maturity->getDomain()->getPosition()] = $maturity->getDomain()->getName();
            }
        }

        $section->addTitle('Évaluation de la mise en conformité', 2);

        $section->addText('Afin de répondre aux objectifs du RGPD, la gestion des données à caractère personnel est structurée en ' . \count($domainsName) . ' domaines.');
        $section->addText('Chacun des ' . \count($domainsName) . ' domaines a été évalué par la structure selon l’échelle de maturité ci-après.');
        $section->addText("Echelle d'estimation de maturité", ['bold' => true]);

        $tableStyleConformite = [
            'borderColor' => '006699',
            'borderSize'  => 6,
            'cellMargin'  => 100,
            'unit'        => TblWidth::PERCENT,
            'width'       => 100 * 50,
            'cantsplit'   => true,
        ];

        $table = $section->addTable($tableStyleConformite);
        $row   = $table->addRow(null, ['valign' => 'center', 'tblHeader' => true, 'cantsplit' => true]);
        $cell  = $row->addCell(750, ['bgColor' => '3c8dbc', 'valign' => 'center']);
        $cell->addText('Pratique', ['bold' => true, 'color' => 'ffffff']);
        $cell = $row->addCell(null, ['bgColor' => '3c8dbc', 'gridSpan' => 2, 'valign' => 'center']);
        $cell->addText('Critère d\'appréciation', ['bold' => true, 'color' => 'ffffff']);

        $row  = $table->addRow(null, ['cantsplit' => true]);
        $cell = $row->addCell(750, ['bgColor' => 'ffb3b3', 'valign' => 'center']);
        $cell->addText('0');
        $cell = $row->addCell(1250);
        $cell->addText('Inexistant');
        $cell = $row->addCell(3000);
        $cell->addText(self::RESPONSE_0 . ' (en dessous de 30%)');

        $row  = $table->addRow(null, ['cantsplit' => true]);
        $cell = $row->addCell(750, ['bgColor' => 'ffff80', 'valign' => 'center']);
        $cell->addText('1');
        $cell = $row->addCell(1250);
        $cell->addText('Partielle');
        $cell = $row->addCell(3000);
        $cell->addText(self::RESPONSE_1 . ' (entre 30% et 70%)');

        $row  = $table->addRow(null, ['cantsplit' => true]);
        $cell = $row->addCell(750, ['bgColor' => '85e085', 'valign' => 'center']);
        $cell->addText('2');
        $cell = $row->addCell(1250);
        $cell->addText('Conforme');
        $cell = $row->addCell(3000);
        $cell->addText(self::RESPONSE_2 . ' (plus de 70%)');

        $serie1 = [];
        $serie2 = [];

        // Radar
        \ksort($maturityList);
        \ksort($domainsName);

        foreach ($maturityList as $score) {
            if (isset($score['old'])) {
                $serie2[] = $score['old'] / 10;
            }
            $serie1[] = $score['new'] / 10;
        }
        // Display
        $section->addTitle("Résultat de l'évaluation du {$data['new']->getCreatedAt()->format('d/m/Y')}", 2);

        $chart = $section->addChart(
            'radar',
            $domainsName,
            $serie1,
            [
                'height' => Converter::cmToEmu(11),
                'width'  => Converter::cmToEmu(15),
                '3d'     => true,
            ]
        );
        if (!empty($serie2)) {
            if (isset($data['old'])) {
                $chart->addSeries(\array_keys($maturityList), $serie2, $data['old']->getCreatedAt()->format('d/m/Y'));
            }
        }
        $table = $section->addTable(['unit' => TblWidth::PERCENT, 'width' => 5000]);
        $row   = $table->addRow(null, ['tblHeader' => true, 'cantsplit' => true]);
        if (!empty($serie2)) {
            $cell = $row->addCell(2500);
            if (isset($data['old'])) {
                $cell->addText("{$data['old']}", ['color' => 'b30000']);
            }
        }
        $cell = $row->addCell(2500);
        $cell->addText("{$data['new']}", ['align' => 'right', 'color' => '3c8dbc']);

        $tablebis = $section->addTable($tableStyleConformite);
        $row      = $tablebis->addRow(null, ['valign' => 'center', 'tblHeader' => true, 'cantsplit' => true]);
        $cell     = $row->addCell(2000, ['bgColor' => '3c8dbc', 'valign' => 'center']);
        $cell->addText('Domaines', ['bold' => true, 'color' => 'ffffff']);
        $cell = $row->addCell(2000, ['bgColor' => '3c8dbc', 'valign' => 'center']);
        if (isset($data['old'])) {
            $cell->addText($data['old']->getCreatedAt()->format('d/m/Y'), ['bold' => true, 'color' => 'ffffff']);
        }
        $cell = $row->addCell(2000, ['bgColor' => '3c8dbc', 'valign' => 'center']);
        $cell->addText($data['new']->getCreatedAt()->format('d/m/Y'), ['bold' => true, 'color' => 'ffffff']);

        $i = 0;
        foreach ($domainsName as $domain) {
            $row  = $tablebis->addRow(null, ['valign' => 'center', 'cantsplit' => true]);
            $cell = $row->addCell(2000, ['valign' => 'center']);
            $cell->addText($domain, []);
            $cell = $row->addCell(2000, ['valign' => 'center']);
            $cell->addText($serie1[$i], []);

            $cell = $row->addCell(2000, ['valign' => 'center']);
            if (isset($serie2[$i])) {
                $cell->addText($serie2[$i], []);
            }
            ++$i;
        }
    }

    public function addSyntheticView(Section $section, array $data): void
    {
        $maturityList = [];
        $domainsName  = [];
        if (isset($data['old'])) {
            foreach ($data['old']->getMaturity() as $maturity) {
                $maturityList[$maturity->getDomain()->getPosition()]['old'] = $maturity->getScore();
                if (!isset($domainsName[$maturity->getDomain()->getPosition()])) {
                    $domainsName[$maturity->getDomain()->getPosition()] = $maturity->getDomain()->getName();
                }
            }
        }
        foreach ($data['new']->getMaturity() as $maturity) {
            $maturityList[$maturity->getDomain()->getPosition()]['new'] = $maturity->getScore();
            if (!isset($domainsName[$maturity->getDomain()->getPosition()])) {
                $domainsName[$maturity->getDomain()->getPosition()] = $maturity->getDomain()->getName();
            }
        }
        \ksort($maturityList);
        \ksort($domainsName);

        $serie1 = [];
        $serie2 = [];

        // Table header
        $tableData = [
            [
                '',
            ],
        ];
        if (isset($data['old'])) {
            $tableData[0][] = $this->getDate($data['old']->getCreatedAt(), 'd/m/Y');
        }
        $tableData[0][] = $this->getDate($data['new']->getCreatedAt(), 'd/m/Y');
        // Table data + radar data
        foreach ($maturityList as $position => $score) {
            $row   = [];
            $row[] = $domainsName[$position];
            if (isset($score['old'])) {
                $row[]    = $score['old'] / 10; // Display comma with 1 digit precision
                $serie2[] = $score['old'] / 10;
            }
            if (isset($score['new'])) {
                $row[]       = $score['new'] / 10; // Display comma with 1 digit precision
                $serie1[]    = $score['new'] / 10;
                $tableData[] = $row;
            }
        }
        // Display
        $section->addTitle('Vue d\'ensemble', 1);
        $this->addTable($section, $tableData, true, self::TABLE_ORIENTATION_HORIZONTAL);

        $section->addTextBreak(2);

        $chart = $section->addChart(
            'radar',
            $domainsName,
            $serie1,
            [
                'height' => Converter::cmToEmu(11),
                'width'  => Converter::cmToEmu(15),
            ]
        );
        $chart->getStyle()->setCategoryLabelPosition('high');
        if (!empty($serie2)) {
            $chart->addSeries(\array_keys($maturityList), $serie2);
        }
        $section->addPageBreak();
    }

    public function addDetailedView(Section $section, array $data): void
    {
        $section->addTitle('Détail', 1);

        $index = [
            'old' => 1,
            'new' => 2,
        ];

        // Order data
        $ordered = [];
        /**
         * @var int    $key
         * @var Survey $survey
         */
        foreach ($data as $key => $survey) {
            foreach ($survey->getAnswers() as $answer) {
                $ordered[$answer->getQuestion()->getDomain()->getName()][$answer->getQuestion()->getName()][$key] = $answer;
            }
            foreach ($survey->getOptionalAnswers() as $optionalAnswer) {
                $ordered[$optionalAnswer->getQuestion()->getDomain()->getName()][$optionalAnswer->getQuestion()->getName()][$key] = $optionalAnswer;
            }
        }

        \ksort($ordered);

        // Table Header
        $tableHeader = [
            [
                '',
            ],
        ];
        if (isset($data['old'])) {
            $tableHeader[0][] = isset($data['old']) ? $this->getDate($data['old']->getCreatedAt(), 'd/m/Y') : '';
        }
        $tableHeader[0][] = $this->getDate($data['new']->getCreatedAt(), 'd/m/Y');

        // Table Body
        foreach ($ordered as $domainName => $questions) {
            $section->addTitle($domainName, 3);
            $parsedData = $tableHeader;
            \ksort($questions);
            foreach ($questions as $questionName => $questionItem) {
                $table   = [];
                $table[] = $questionName;
                /**
                 * @var string                $newOld
                 * @var Answer|OptionalAnswer $answer
                 */
                foreach ($questionItem as $newOld => $answer) {
                    $table[$index[$newOld]] = Answer::class === get_class($answer) ? $answer->getName() : 'Non concerné : ' . $answer->getReason();
                }
                \ksort($table);
                $parsedData[] = $table;
            }
            $this->addTable($section, $parsedData, true, self::TABLE_ORIENTATION_VERTICAL);
        }
    }
}
