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

use App\Domain\Registry\Dictionary\ViolationCauseDictionary;
use App\Domain\Registry\Dictionary\ViolationCommunicationDictionary;
use App\Domain\Registry\Dictionary\ViolationConcernedDataDictionary;
use App\Domain\Registry\Dictionary\ViolationConcernedPeopleDictionary;
use App\Domain\Registry\Dictionary\ViolationGravityDictionary;
use App\Domain\Registry\Dictionary\ViolationImpactDictionary;
use App\Domain\Registry\Dictionary\ViolationNatureDictionary;
use App\Domain\Registry\Dictionary\ViolationNotificationDictionary;
use App\Domain\Registry\Dictionary\ViolationOriginDictionary;
use App\Domain\Registry\Model\Violation;
use PhpOffice\PhpWord\Element\Section;

class ViolationGenerator extends AbstractGenerator implements ImpressionGeneratorInterface
{
    /**
     * Global overview : Information to display for violation in overview report.
     *
     * @throws \Exception
     */
    public function addGlobalOverview(Section $section, array $data): void
    {
        $collectivity = $this->userProvider->getAuthenticatedUser()->getCollectivity();

        $nbTotal = \count($data);

        foreach ($data as $violation) {
            $cellDate   = [];
            $cellDate[] = $this->getDate($violation->getDate(), 'd/m/Y');
            if ($violation->isInProgress()) {
                $cellDate[] = '(Toujours en cours)';
            }
            $tableData[] = [
                $cellDate,
                ViolationNatureDictionary::getNatures(),
                ViolationCauseDictionary::getNatures()[$violation->getCause()],
                ViolationGravityDictionary::getGravities()[$violation->getGravity()],
                'test CNIL',
                'test Concernes',
            ];
        }

        $section->addTitle('Registre des violations de données', 2);

        $section->addText("Un registre des violations de données à caractère personnel est tenu à jour par '{$collectivity}'.");

        if (0 === $nbTotal) {
            $section->addText('Un modèle de registre des violations de données est opérationnel et tenu à jour. A ce jour, il n’y a pas eu de violations de données à caractère personnel.');
        } else {
            if (1 === $nbTotal) {
                $section->addText("Il y a eu {$nbTotal} violation de données à caractère personnel.");
            } else {
                $section->addText("Il y a eu {$nbTotal} violations de données à caractère personnel.");
            }
            $violationTable = $section->addTable($this->tableStyle);
            $violationTable->addRow(null, ['tblHeader' => true, 'cantsplit' => true]);
            $ViolationNames = [
                ['name' => 'Date', 'width' => 1000, 'merge' => 'restart'],
                ['name' => 'Nature', 'width' => 1500, 'merge' => 'restart'],
                ['name' => 'Cause', 'width' => 1500, 'merge' => 'restart'],
                ['name' => 'Niveau de gravité', 'width' => 1500, 'merge' => 'restart'],
                ['name' => 'Notification', 'width' => 3000, 'merge' => null],
            ];
            foreach ($ViolationNames as $item) {
                $cell = $violationTable->addCell($item['width'], ['bgColor' => '3c8dbc', 'vMerge' => $item['merge']]);
                if ('Notification' === $item['name']) {
                    $cell->getStyle()->setGridSpan(2);
                }
                $cell->addText($item['name'], $this->textHeadStyle);
            }
            $violationTable->addRow(null, ['tblHeader' => true, 'cantsplit' => true]);
            $violationTable->addCell(1000, ['vMerge' => 'continue']);
            $violationTable->addCell(1500, ['vMerge' => 'continue']);
            $violationTable->addCell(1500, ['vMerge' => 'continue']);
            $violationTable->addCell(1500, ['vMerge' => 'continue']);
            $cell = $violationTable->addCell(1500, ['bgColor' => '3c8dbc', 'vAlign' => 'center']);
            $cell->addText('CNIL', $this->textHeadStyle);
            $cell = $violationTable->addCell(1500, ['bgColor' => '3c8dbc', 'vAlign' => 'center']);
            $cell->addText('Concernés', $this->textHeadStyle);

            foreach ($tableData as $key => $row) {
                $violationTable->addRow(null, ['cantsplit' => true]);
                foreach ($row as $cellItem) {
                    $cell = $violationTable->addCell(0 === $key ? 1000 : 1500);
                    if (is_array($cellItem)) {
                        foreach ($cellItem as $item) {
                            $cell->addListItem($item, (int) null, [], [], ['spaceAfter' => 0]);
                        }
                    } else {
                        $cell->addText($cellItem);
                    }
                }
            }
        }
    }

    public function addSyntheticView(Section $section, array $data): void
    {
        $section->addTitle('Liste des violations', 1);

        // Aggregate data before rendering
        $tableData = [
            [
                'Date',
                'Nature',
                'Cause',
                'Niveau de gravité',
            ],
        ];

        foreach ($data as $violation) {
            /** @var Violation $violation */
            $cellDate   = [];
            $cellDate[] = $this->getDate($violation->getDate(), 'd/m/Y');
            if ($violation->isInProgress()) {
                $cellDate[] = '(Toujours en cours)';
            }
            $natures     = join(', ', array_map(function ($n) { return ViolationNatureDictionary::getNatures()[$n] ?? $n; }, (array) $violation->getViolationNatures()));
            $tableData[] = [
                $cellDate,
                $natures,
                ViolationCauseDictionary::getNatures()[$violation->getCause()],
                ViolationGravityDictionary::getGravities()[$violation->getGravity()],
            ];
        }

        // Rendering
        $this->addTable($section, $tableData, true, self::TABLE_ORIENTATION_HORIZONTAL);
        $section->addPageBreak();
    }

    public function addDetailedView(Section $section, array $data): void
    {
        $section->addTitle('Détail des violations', 1);

        foreach ($data as $key => $violation) {
            if (0 !== $key) {
                $section->addPageBreak();
            }

            $section->addTitle((string) $violation, 2);

            $cellDate   = [];
            $cellDate[] = $this->getDate($violation->getDate(), 'd/m/Y');
            if ($violation->isInProgress()) {
                $cellDate[] = '(Toujours en cours)';
            }
            $natures = join(', ', array_map(function ($n) { return ViolationNatureDictionary::getNatures()[$n] ?? $n; }, (array) $violation->getViolationNatures()));

            $generalInformationData = [
                [
                    'Date de la violation',
                    $cellDate,
                ],
                [
                    'Natures de la violation',
                    $natures,
                ],
                [
                    'Origine de la perte de données',
                    $this->translateWithDictionary(ViolationOriginDictionary::getOrigins(), $violation->getOrigins()),
                ],
                [
                    'Cause de la violation',
                    $this->translateWithDictionary(ViolationCauseDictionary::getNatures(), $violation->getCause()),
                ],
                [
                    'Nature des données concernées',
                    $this->translateWithDictionary(ViolationConcernedDataDictionary::getConcernedData(), $violation->getConcernedDataNature()),
                ],
                [
                    'Catégorie des personnes concernées',
                    $this->translateWithDictionary(ViolationConcernedPeopleDictionary::getConcernedPeople(), $violation->getConcernedPeopleCategories()),
                ],
                [
                    'Nombre approximatif d\'enregistrements concernés par la violation',
                    $violation->getNbAffectedRows(),
                ],
                [
                    'Nombre approximatif de personnes concernés par la violation',
                    $violation->getNbAffectedPersons(),
                ],
            ];

            $consequenceData = [
                [
                    'Nature des impacts potentiels pour les personnes',
                    $this->translateWithDictionary(ViolationImpactDictionary::getImpacts(), $violation->getPotentialImpactsNature()),
                ],
                [
                    'Niveau de gravité',
                    $this->translateWithDictionary(ViolationGravityDictionary::getGravities(), $violation->getGravity()),
                ],
                [
                    'Communications aux personnes concernées',
                    $this->translateWithDictionary(ViolationCommunicationDictionary::getCommunications(), $violation->getCommunication()),
                ],
                [
                    'Précisions sur les communications',
                    $violation->getCommunicationPrecision(),
                ],
                [
                    'Mesures techniques et organisationnelles appliquées suite à la violation',
                    $violation->getAppliedMeasuresAfterViolation(),
                ],
                [
                    'Notification',
                    $this->translateWithDictionary(ViolationNotificationDictionary::getNotifications(), $violation->getNotification()),
                ],
                [
                    'Précisions sur les notifications',
                    $violation->getNotificationDetails(),
                ],
                [
                    'Commentaire',
                    $violation->getComment(),
                ],
            ];

            $historyData = [
                [
                    'Créateur',
                    strval($violation->getCreator()),
                ],
                [
                    'Date de création',
                    $this->getDate($violation->getCreatedAt()),
                ],
                [
                    'Date de modification',
                    $this->getDate($violation->getUpdatedAt()),
                ],
            ];

            $section->addTitle('Informations sur la violation', 3);
            $this->addTable($section, $generalInformationData, true, self::TABLE_ORIENTATION_VERTICAL);

            $section->addTitle('Conséquences de la violation', 3);
            $this->addTable($section, $consequenceData, true, self::TABLE_ORIENTATION_VERTICAL);

            $section->addTitle('Historique', 3);
            $this->addTable($section, $historyData, true, self::TABLE_ORIENTATION_VERTICAL);
        }
    }

    private function translateWithDictionary(array $dictionaryData = [], $value = null): array
    {
        if (\is_null($value)) {
            return [];
        }

        if (!\is_array($value)) {
            return [$dictionaryData[$value]];
        }

        // Value is iterable
        $translatedValues = [];
        foreach ($value as $item) {
            $translatedValues[] = "- {$dictionaryData[$item]}";
        }

        return $translatedValues;
    }

    public function AnnexeList($section, $violations)
    {
        $section->addTitle('Liste des violations de données', 2);
        $tableViolationAnnexeApplied = $section->addTable($this->tableStyle);
        $tableViolationAnnexeApplied->addRow(null, ['tblHeader' => true, 'cantSplit' => true]);
        $cell = $tableViolationAnnexeApplied->addCell(1000, $this->cellHeadStyle);
        $cell->addText('Date', $this->textHeadStyle);
        $cell = $tableViolationAnnexeApplied->addCell(1000, $this->cellHeadStyle);
        $cell->addText('Communication aux personnes', $this->textHeadStyle);
        $cell = $tableViolationAnnexeApplied->addCell(2000, $this->cellHeadStyle);
        $cell->addText('Notification autorité de contrôle', $this->textHeadStyle);
        $cell = $tableViolationAnnexeApplied->addCell(2000, $this->cellHeadStyle);
        $cell->addText('Sous-traitants', $this->textHeadStyle);
        $cell = $tableViolationAnnexeApplied->addCell(2000, $this->cellHeadStyle);
        $cell->addText('Traitements associés', $this->textHeadStyle);

        foreach ($violations as $line) {
            $tableViolationAnnexeApplied->addRow(null, ['cantSplit' => true]);
            $cell = $tableViolationAnnexeApplied->addCell(1000);
            $date = !$line->isInProgress() ? $line->getDate()->format('d/m/Y') : $line->getDate()->format('d/m/Y') . ' (En cours)';
            $cell->addText($date);
            $cell = $tableViolationAnnexeApplied->addCell(1000);
            $cell->addText($line->getCommunicationPrecision());
            $cell = $tableViolationAnnexeApplied->addCell(2000);
            $cell->addText($line->getNotificationDetails());
            $cell = $tableViolationAnnexeApplied->addCell(2000);
            foreach ($line->getContractors() as $item) {
                $cell->addListItem($item->getName(), null, [], [], ['spaceAfter' => 0]);
            }
            $cell = $tableViolationAnnexeApplied->addCell(2000);
            foreach ($line->getTreatments() as $item) {
                $cell->addListItem($item->getName(), null, [], [], ['spaceAfter' => 0]);
            }
        }
        $section->addPageBreak();
    }
}
