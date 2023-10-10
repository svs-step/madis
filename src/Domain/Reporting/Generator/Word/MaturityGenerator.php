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
use App\Domain\Registry\Model\Mesurement;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Shared\Converter;

class MaturityGenerator extends AbstractGenerator implements ImpressionGeneratorInterface
{
    public function addContextView(Section $section, array $data): void
    {
        if (isset($data['bilanReport']) && $data['bilanReport']) {
            $section->addTitle('Évaluation de la mise en conformité', 2);
            $section->addTitle('Contexte', 3);
        } else {
            $section->addTitle('Contexte', 1);
        }

        $table = $section->addTable([
            'borderColor' => '006699',
            'borderSize'  => 6,
            'cellMargin'  => 100,
        ]);

        $cellStyle = ['bgColor' => '3c8dbc'];
        $textStyle = ['bold' => true, 'color' => 'ffffff'];
        $table->addRow();
        $cell = $table->addCell(3000, $cellStyle);
        $cell->addText('Référentiel', $textStyle);
        $cell = $table->addCell(7000);
        $cell->addText($data['new']->getReferentiel()->getName());
        $table->addRow();
        $cell = $table->addCell(3000, $cellStyle);
        $cell->addText('Description', $textStyle);
        $cell = $table->addCell(7000);
        $cell->addText($data['new']->getReferentiel()->getDescription());
        $table->addRow();
        $cell = $table->addCell(3000, $cellStyle);
        $cell->addText('Date de l\'indice de maturité', $textStyle);
        $cell = $table->addCell(7000);
        $cell->addText($data['new']->getCreatedAt()->format('d/m/Y'));
        $table->addRow();
        $cell = $table->addCell(3000, $cellStyle);
        $cell->addText('Date de l\'indice de maturité précédent', $textStyle);
        $cell = $table->addCell(7000);
        $cell->addText(isset($data['old']) ? $data['old']->getCreatedAt()->format('d/m/Y') : 'Aucun');
    }

    public function addSyntheticView(Section $section, array $data): void
    {
        if (isset($data['bilanReport']) && $data['bilanReport']) {
            $section->addTitle("Résultat de l'évaluation", 3);
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
        if (!(isset($data['bilanReport']) && $data['bilanReport'])) {
            $section->addTitle('Vue d\'ensemble', 1);
        }

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

        // Order data
        $ordered      = [];
        $descriptions = [];
        /**
         * @var Survey $survey
         */
        $survey = $data['new'];
        foreach ($survey->getAnswerSurveys() as $answerSurvey) {
            $answer                                                                                     = $answerSurvey->getAnswer();
            $ordered[$answer->getQuestion()->getDomain()->getName()][$answer->getQuestion()->getName()] = $answer;
            $descriptions[$answer->getQuestion()->getDomain()->getName()]                               = $answer->getQuestion()->getDomain()->getDescription();
        }
        foreach ($survey->getOptionalAnswers() as $optionalAnswer) {
            $ordered[$optionalAnswer->getQuestion()->getDomain()->getName()][$optionalAnswer->getQuestion()->getName()] = $optionalAnswer;
            $descriptions[$optionalAnswer->getQuestion()->getDomain()->getName()]                                       = $optionalAnswer->getQuestion()->getDomain()->getDescription();
        }

        \ksort($ordered);
        // Table Body
        foreach ($ordered as $domainName => $questions) {
            $section->addTitle($domainName, 3);
            $section->addText($descriptions[$domainName]);
            $parsedData = [['', 'Réponse', 'Préconisation', 'Actions de protection']];
            \ksort($questions);
            foreach ($questions as $questionName => $answer) {
                $table   = [];
                $table[] = $questionName;
                /*
                 * @var string                $newOld
                 * @var Answer|OptionalAnswer $answer
                 */
                $table[1]    = is_a($answer, Answer::class) ? $answer->getName() : $answer->getQuestion()->getOptionReason();
                $table[2]    = is_a($answer, Answer::class) ? $answer->getRecommendation() : '';
                $mesurements = '';
                if ($survey->getAnswerSurveys()) {
                    foreach ($survey->getAnswerSurveys() as $answerSurvey) {
                        if ($answerSurvey->getAnswer()->getId() === $answer->getId()) {
                            $mesurements = join(', ', $answerSurvey->getMesurements()->map(function (Mesurement $mesurement) {
                                return $mesurement->getName();
                            })->toArray());
                        }
                    }
                }
                $table[3] = $mesurements;

                \ksort($table);
                $parsedData[] = $table;
            }

            $this->addTable($section, $parsedData, true, self::TABLE_ORIENTATION_VERTICAL);
        }
    }
}
