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

use App\Domain\Registry\Dictionary\MesurementPriorityDictionary;
use App\Domain\Registry\Dictionary\MesurementStatusDictionary;
use App\Domain\Registry\Model\Mesurement;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\TblWidth;

class MesurementGenerator extends AbstractGenerator implements ImpressionGeneratorInterface
{
    /**
     * Global overview : Information to display for mesurement in overview report.
     */
    public function addGlobalOverview(Section $section, array $data): void
    {
        $collectivity = $this->userProvider->getAuthenticatedUser()->getCollectivity();
        // Aggregate data before rendering
        $appliedMesurement = [
            [
                'ACTION',
                'DATE',
                'OBSERVATIONS',
            ],
        ];
        $actionPlan = [
            [
                'ACTION',
                'DATE',
                'OBSERVATIONS',
                'RESPONSABLE DE L\'ACTION',
            ],
        ];

        uasort($data, [$this, 'sortMesurementByPriority']);

        foreach ($data as $mesurement) {
            if (MesurementStatusDictionary::STATUS_APPLIED === $mesurement->getStatus()) {
                $appliedMesurement[] = [
                    $mesurement->getName(),
                    $mesurement->getPlanificationDate() ? $mesurement->getPlanificationDate()->format(self::DATE_FORMAT) : '',
                    $mesurement->getComment(),
                ];
            } elseif (!\is_null($mesurement->getPlanificationDate()) && MesurementStatusDictionary::STATUS_NOT_APPLIED === $mesurement->getStatus()) {
                $actionPlan[] = [
                    'data'  => [
                        $mesurement->getName(),
                        $mesurement->getPlanificationDate() ? $mesurement->getPlanificationDate()->format(self::DATE_FORMAT) : '',
                        $mesurement->getComment(),
                        $mesurement->getManager(),
                    ],
                    'style' => [
                        'bgColor' => $mesurement->getPriority() ? MesurementPriorityDictionary::getPrioritiesColors()[$mesurement->getPriority()] : '',
                    ],
                ];
            }
        }

        $section->addTitle('Actions de protection mises en place', 2);
        $section->addText("Afin de protéger les données à caractère personnel, '{$collectivity}' a mis en place les actions de protection suivantes :");
        $this->addTable($section, $appliedMesurement, true, self::TABLE_ORIENTATION_HORIZONTAL);

        $section->addTitle("Plan d'actions", 2);
        $section->addText('Un plan d’action a été établi comme suit.');
        $this->addTable($section, $actionPlan, true, self::TABLE_ORIENTATION_HORIZONTAL);

        $section->addTextBreak();

        $table = $section->addTable([
            'borderColor' => '006699',
            'borderSize'  => 6,
            'width'       => 50 * 50,
            'unit'        => TblWidth::PERCENT,
            'alignment'   => Jc::CENTER,
            'layout'      => \PhpOffice\PhpWord\Style\Table::LAYOUT_FIXED,
        ]);
        $row  = $table->addRow(null, ['valign' => 'center']);
        $cell = $row->addCell(null, ['valign' => 'center', 'gridSpan' => 3]);
        $cell->addText('Légende priorité', ['bold' => true, 'size' => 8], ['align' => 'center']);

        $priorities = MesurementPriorityDictionary::getPrioritiesNameWithoutNumber();
        $row        = $table->addRow();
        foreach ($priorities as $key => $priority) {
            $cell = $row->addCell(null, ['valign' => 'center', 'bgColor' => MesurementPriorityDictionary::getPrioritiesColors()[$key]]);
            $cell->addText($priority, ['bold' => true, 'size' => 8], ['align' => 'center']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addSyntheticView(Section $section, array $data): void
    {
        $section->addTitle('Liste des actions de protection', 1);

        // Table data
        // Add header
        $tableData = [
            [
                'Nom',
                'Statut',
                'Priorité',
            ],
        ];

        uasort($data, [$this, 'sortMesurementByPriority']);

        // Add content
        foreach ($data as $mesurement) {
            $tableData[] = [
                $mesurement->getName(),
                MesurementStatusDictionary::getStatus()[$mesurement->getStatus()],
                $mesurement->getPriority() ? MesurementPriorityDictionary::getPriorities()[$mesurement->getPriority()] : '',
            ];
        }

        $this->addTable($section, $tableData, true, self::TABLE_ORIENTATION_HORIZONTAL);
    }

    /**
     * {@inheritdoc}
     */
    public function addDetailedView(Section $section, array $data): void
    {
        $section->addTitle('Détail des actions de protection', 1);

        foreach ($data as $key => $mesurement) {
            if (0 != $key) {
                $section->addPageBreak();
            }
            $section->addTitle($mesurement->getName(), 2);

            $generalInformationsData = [
                [
                    'Nom',
                    $mesurement->getName(),
                ],
                [
                    'Description',
                    $mesurement->getDescription() ? \preg_split('/\R/', $mesurement->getDescription()) : null,
                ],
                [
                    'Responsable d\'action',
                    $mesurement->getManager(),
                ],
                [
                    'Priorité',
                    !\is_null($mesurement->getPriority()) ? MesurementPriorityDictionary::getPriorities()[$mesurement->getPriority()] : '',
                ],
                [
                    'Coût',
                    $mesurement->getCost(),
                ],
                [
                    'Charge',
                    $mesurement->getCharge(),
                ],
            ];

            $applicationData = [
                [
                    'Statut',
                    MesurementStatusDictionary::getStatus()[$mesurement->getStatus()],
                ],
                [
                    'Planification',
                    MesurementStatusDictionary::STATUS_NOT_APPLICABLE !== $mesurement->getStatus()
                        ? ($mesurement->getPlanificationDate() ? $this->getDate($mesurement->getPlanificationDate()) : null)
                        : 'Non applicable',
                ],
            ];

            $historyData = [
                [
                    'Créateur',
                    strval($mesurement->getCreator()),
                ],
                [
                    'Date de création',
                    $this->getDate($mesurement->getCreatedAt()),
                ],
                [
                    'Dernière mise à jour',
                    $this->getDate($mesurement->getUpdatedAt()),
                ],
            ];

            $section->addTitle('Informations générales', 3);
            $this->addTable($section, $generalInformationsData, true, self::TABLE_ORIENTATION_VERTICAL);

            $section->addTitle('Application', 3);
            $this->addTable($section, $applicationData, true, self::TABLE_ORIENTATION_VERTICAL);

            $section->addTitle('Historique', 3);
            $this->addTable($section, $historyData, true, self::TABLE_ORIENTATION_VERTICAL);
        }
    }

    private function sortMesurementByPriority(Mesurement $a, Mesurement $b)
    {
        $weightA = \is_null($a->getPriority()) ? 0 : MesurementPriorityDictionary::getWeightPriorities()[$a->getPriority()];
        $weightB = \is_null($b->getPriority()) ? 0 : MesurementPriorityDictionary::getWeightPriorities()[$b->getPriority()];

        if ($weightA === $weightB) {
            return 0;
        }

        return ($weightA < $weightB) ? 1 : -1;
    }
}
