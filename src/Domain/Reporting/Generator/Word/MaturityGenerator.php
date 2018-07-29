<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan.bourlard@outlook.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\Reporting\Generator\Word;

use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Shared\Converter;

class MaturityGenerator extends AbstractGenerator implements ImpressionGeneratorInterface
{
    public function addSyntheticView(Section $section, array $data): void
    {
        $maturityList = [];
        if (isset($data['old'])) {
            foreach ($data['old']->getMaturity() as $maturity) {
                $maturityList[$maturity->getDomain()->getName()]['old'] = $maturity->getScore();
            }
        }
        foreach ($data['new']->getMaturity() as $maturity) {
            $maturityList[$maturity->getDomain()->getName()]['new'] = $maturity->getScore();
        }

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
        foreach ($maturityList as $domain => $score) {
            $row   = [];
            $row[] = $domain;
            if (isset($score['old'])) {
                $row[]    = $score['old'] / 10; // Display comma with 1 digit precision
                $serie2[] = $score['old'] / 10;
            }
            $row[]       = $score['new'] / 10; // Display comma with 1 digit precision
            $serie1[]    = $score['new'] / 10;
            $tableData[] = $row;
        }
        // Display
        $section->addTitle('Vue d\'ensemble', 1);
        $this->addTable($section, $tableData, true, self::TABLE_ORIENTATION_HORIZONTAL);

        $section->addTextBreak(2);

        $chart      = $section->addChart(
            'radar',
            \array_keys($maturityList),
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
    }
}
