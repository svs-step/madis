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
use PhpOffice\PhpWord\SimpleType\TblWidth;

class MesurementGenerator extends AbstractGenerator implements ImpressionGeneratorInterface
{
    protected $tableStyleConformite;

    /**
     * Global overview : Information to display for mesurement in overview report.
     */
    public function addGlobalOverview(Section $section, array $data): void
    {
        $collectivity = $this->userProvider->getAuthenticatedUser()->getCollectivity();

        $actionPlan = [
            [
                'Priorité',
                'Action',
                'Date',
                'Responsable de l\'action',
                'Observations',
            ],
        ];

        $this->tableStyleConformite = [
            'borderColor' => '006699',
            'borderSize'  => 6,
            'cellMargin'  => 100,
            'unit'        => TblWidth::PERCENT,
            'width'       => 100 * 50,
        ];

        uasort($data, [$this, 'sortMesurementByDate']);

        $appliedMesurement = [];
        $actionPlan        = [];
        foreach ($data as $mesurement) {
            if (MesurementStatusDictionary::STATUS_APPLIED === $mesurement->getStatus()) {
                $appliedMesurement[] = [
                    $mesurement->getCreatedAt()->format('d/m/Y'),
                    $mesurement->getName(),
                ];
            } elseif (!\is_null($mesurement->getPlanificationDate()) && MesurementStatusDictionary::STATUS_NOT_APPLIED === $mesurement->getStatus()) {
                $actionPlan[] = [
                    'data'  => [
                        $mesurement->getPriority(),
                        $mesurement->getName(),
                        $mesurement->getPlanificationDate() ? $mesurement->getPlanificationDate()->format(self::DATE_FORMAT) : '',
                        $mesurement->getManager(),
                        $mesurement->getComment(),
                    ],
                    'style' => [
                        'bgColor' => $mesurement->getPriority() ? $this->ColorCellPriority($mesurement->getPriority()) : '',
                    ],
                ];
            }
        }

        $section->addTitle('Actions de protection mises en place', 2);
        $textrun = $section->addTextRun();
        $textrun->addText("Afin de protéger les données à caractère personnel, '{$collectivity}' a mis en place des actions de protection. Une ");
        $textrun->addLink('ActionsImplemented', 'liste exhaustive des actions de protection mises en place', ['underline' => 'single'], [], true);
        $textrun->addText(' figure en annexe. Ci-dessous, les 20 dernières actions :');

        $this->ProtectionActionAppliedTable($section, $appliedMesurement, false);

        $section->addTitle("Plan d'actions", 2);
        $section->addText('Un plan d’action a été établi comme suit.');
        $this->ActionPlanTable($section, $actionPlan);

        $section->addTextBreak();

        $section->addText('Nombre d’actions de protection affectées par type de registre :');
        $this->ActionTypeTable($section, $data);
    }

    private function ActionTypeTable($section, $data)
    {
        $tableActionType = $section->addTable($this->tableStyleConformite);
        $tableActionType->addRow(null, ['tblHeader' => true, 'cantSplit' => true]);
        $cell = $tableActionType->addCell(6000, $this->cellHeadStyle);
        $cell->addText('Type de registre', $this->textHeadStyle);
        $cell = $tableActionType->addCell(3000, $this->cellHeadStyle);
        $cell->addText('Nombre d’actions de protection affectées', $this->textHeadStyle);

        $cntTreatment   = 0;
        $cntContractors = 0;
        $cntTools       = 0;
        $cntViolations  = 0;
        $cntRequests    = 0;
        foreach ($data as $item) {
            $cntTreatment   = $item->getTreatments() ? $cntTreatment + count($item->getTreatments()) : $cntTreatment;
            $cntContractors = $item->getContractors() ? $cntContractors + count($item->getContractors()) : $cntContractors;
            $cntTools       = $item->getTools() ? $cntTools + count($item->getTools()) : $cntTools;
            $cntViolations  = $item->getViolations() ? $cntViolations + count($item->getViolations()) : $cntViolations;
            $cntRequests    = $item->getRequests() ? $cntRequests + count($item->getRequests()) : $cntRequests;
        }

        $collectivity = $this->userProvider->getAuthenticatedUser()->getCollectivity();
        $arrayTypes   = $collectivity->isHasModuleTools() ?
            [['type' => 'Traitements', 'count' => $cntTreatment], ['type' => 'Sous-traitants', 'count' => $cntContractors], ['type' => 'Logiciels ou supports', 'count' => $cntTools], ['type' => 'Violations de données', 'count' => $cntViolations], ['type' => 'Demandes', 'count' => $cntRequests]] :
            [['type' => 'Traitements', 'count' => $cntTreatment], ['type' => 'Sous-traitants', 'count' => $cntContractors], ['type' => 'Violations de données', 'count' => $cntViolations], ['type' => 'Demandes', 'count' => $cntRequests]];

        foreach ($arrayTypes as $item) {
            $tableActionType->addRow();
            $cell = $tableActionType->addCell(6000);
            $cell->addText($item['type']);
            $cell = $tableActionType->addCell(3000);
            $cell->addText($item['count']);
        }
    }

    public function ProtectionActionAppliedAnnexeTable($section, $data)
    {
        $section->addBookmark('ActionsImplemented');
        $section->addTitle('Liste des actions de protection mises en place', 2);

        uasort($data, [$this, 'sortMesurementByDate']);
        $appliedMesurement = [];
        foreach ($data as $mesurement) {
            if (MesurementStatusDictionary::STATUS_APPLIED === $mesurement->getStatus()) {
                $appliedMesurement[] = [
                    $mesurement->getCreatedAt()->format('d/m/Y'),
                    $mesurement->getName(),
                ];
            }
        }
        $this->ProtectionActionAppliedTable($section, $appliedMesurement, true);
    }

    public function ProtectionActionAppliedTable($section, $appliedMesurement, $isAnnexe)
    {
        $tableProtectionActionApplied = $section->addTable($this->tableStyleConformite);
        $tableProtectionActionApplied->addRow(null, ['tblHeader' => true, 'cantSplit' => true]);
        $cell = $tableProtectionActionApplied->addCell(1500, $this->cellHeadStyle);
        $cell->addText('Date', $this->textHeadStyle);
        $cell = $tableProtectionActionApplied->addCell(7500, $this->cellHeadStyle);
        $cell->addText('Action', $this->textHeadStyle);

        if ($appliedMesurement) {
            $limit = $isAnnexe ? count($appliedMesurement) : 20;

            foreach (array_slice($appliedMesurement, 0, $limit) as $line) {
                $tableProtectionActionApplied->addRow(400, ['exactHeight' => true, 'cantsplit' => true]);
                $cell1 = $tableProtectionActionApplied->addCell(1500);
                $cell1->addText($line[0], [], ['alignment' => 'center']);
                $cell2 = $tableProtectionActionApplied->addCell(7500);
                $cell2->addText($line[1]);
            }
        }
    }

    private function ActionPlanTable($section, $actionPlan)
    {
        $tableActionPlan = $section->addTable($this->tableStyleConformite);
        $tableActionPlan->addRow(null, ['tblHeader' => true, 'cantSplit' => true]);
        $cell = $tableActionPlan->addCell(1000, $this->cellHeadStyle);
        $cell->addText('Priorité', $this->textHeadStyle);
        $cell = $tableActionPlan->addCell(2000, $this->cellHeadStyle);
        $cell->addText('Action', $this->textHeadStyle);
        $cell = $tableActionPlan->addCell(1000, $this->cellHeadStyle);
        $cell->addText('Date', $this->textHeadStyle);
        $cell = $tableActionPlan->addCell(1500, $this->cellHeadStyle);
        $cell->addText('Responsable de l\'action', $this->textHeadStyle);
        $cell = $tableActionPlan->addCell(1500, $this->cellHeadStyle);
        $cell->addText('Observations', $this->textHeadStyle);

        if ($actionPlan) {
            foreach ($actionPlan as $line) {
                $tableActionPlan->addRow(null, ['cantSplit' => true]);
                $cell1    = $tableActionPlan->addCell(1000, ['bgColor' => $line['style']['bgColor']]);
                $priority = match ($line['data'][0]) {
                    'low'    => 'Basse',
                    'normal' => 'Normale',
                    'high'   => 'Haute',
                    null     => 'Aucune',
                };
                $cell1->addText($priority, ['bold' => true]);
                $cell2 = $tableActionPlan->addCell(2000);
                $cell2->addText($line['data'][1]);
                $cell1 = $tableActionPlan->addCell(1000);
                $cell1->addText($line['data'][2]);
                $cell2 = $tableActionPlan->addCell(1500);
                $cell2->addText($line['data'][3]);
                $cell2 = $tableActionPlan->addCell(1500);
                $cell2->addText($line['data'][4]);
            }
        }
    }

    private function ColorCellPriority($priority)
    {
        $returned_value = match ($priority) {
            'low'    => 'ffff80',
            'normal' => 'fac9ad',
            'high'   => 'ffa7a7',
            ''       => 'ffffff',
        };

        return $returned_value;
    }

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
                [
                    'Observations',
                    $mesurement->getComment(),
                ],
            ];

            $historyData = [
                [
                    'Date de création',
                    $this->getDate($mesurement->getCreatedAt()),
                ],
                [
                    'Date de modification',
                    $this->getDate($mesurement->getUpdatedAt()),
                ],
                [
                    'Modifié par ',
                    $mesurement->getUpdatedBy(),
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

    private function sortMesurementByDate(Mesurement $a, Mesurement $b)
    {
        if ($a->getCreatedAt() === $b->getCreatedAt()) {
            return 0;
        }

        return ($a->getCreatedAt() < $b->getCreatedAt()) ? 1 : -1;
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
