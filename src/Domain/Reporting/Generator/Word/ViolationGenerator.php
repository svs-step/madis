<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan@awkan.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\Reporting\Generator\Word;

use App\Domain\Registry\Dictionary\ViolationCauseDictionary;
use App\Domain\Registry\Dictionary\ViolationGravityDictionary;
use App\Domain\Registry\Dictionary\ViolationNatureDictionary;
use PhpOffice\PhpWord\Element\Section;

class ViolationGenerator extends AbstractGenerator implements ImpressionGeneratorInterface
{
    public function addGlobalOverview(Section $section, array $data): void
    {
        $collectivity = $this->userProvider->getAuthenticatedUser()->getCollectivity();

        // Aggregate data before rendering
        $tableData = [
            [
                'Date',
                'Nature',
                'Cause',
                'Niveau de gravité',
            ],
        ];
        $nbTotal = \count($data);
        foreach ($data as $violation) {
            $cellDate   = [];
            $cellDate[] = $this->getDate($violation->getDate(), 'd/m/Y');
            if ($violation->isInProgress()) {
                $cellDate[] = '(Toujours en cours)';
            }
            $tableData[] = [
                $cellDate,
                ViolationNatureDictionary::getNatures()[$violation->getViolationNature()],
                ViolationCauseDictionary::getNatures()[$violation->getCause()],
                ViolationGravityDictionary::getGravities()[$violation->getGravity()],
            ];
        }

        $section->addTitle('Registre des violations de données', 2);
        $section->addText("Un registre des violations de données à caractère personnel est tenu à jour par '{$collectivity}'.");
        $section->addText("Il y a eu {$nbTotal} violations de données à caractère personnel.");

        if (0 < $nbTotal) {
            $this->addTable($section, $tableData, true, self::TABLE_ORIENTATION_HORIZONTAL);
        }
    }

    public function addSyntheticView(Section $section, array $data): void
    {
        // TODO
    }

    public function addDetailedView(Section $section, array $data): void
    {
        // TODO
    }
}
