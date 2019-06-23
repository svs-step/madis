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

use App\Domain\Registry\Dictionary\MesurementStatusDictionary;
use PhpOffice\PhpWord\Element\Section;

class MesurementGenerator extends AbstractGenerator implements ImpressionGeneratorInterface
{
    /**
     * Global overview : Information to display for mesurement in overview report.
     *
     * @param Section $section
     * @param array   $data
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
            ],
        ];

        foreach ($data as $mesurement) {
            if (MesurementStatusDictionary::STATUS_APPLIED === $mesurement->getStatus()) {
                $appliedMesurement[] = [
                    $mesurement->getName(),
                    $mesurement->getPlanificationDate() ? $mesurement->getPlanificationDate()->format(self::DATE_FORMAT) : '',
                    $mesurement->getComment(),
                ];
            } elseif (!\is_null($mesurement->getPlanificationDate()) && MesurementStatusDictionary::STATUS_NOT_APPLIED === $mesurement->getStatus()) {
                $actionPlan[] = [
                    $mesurement->getName(),
                    $mesurement->getPlanificationDate() ? $mesurement->getPlanificationDate()->format(self::DATE_FORMAT) : '',
                    $mesurement->getComment(),
                ];
            }
        }

        $section->addTitle('Actions de protection mises en place', 2);
        $section->addText("Afin de protéger les données à caractère personnel, '{$collectivity}' a mis en place les actions de protection suivantes :");
        $this->addTable($section, $appliedMesurement, true, self::TABLE_ORIENTATION_HORIZONTAL);

        $section->addTitle("Plan d'actions", 2);
        $section->addText('Un plan d’action a été établi comme suit.');
        $this->addTable($section, $actionPlan, true, self::TABLE_ORIENTATION_HORIZONTAL);
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
            ],
        ];
        // Add content
        foreach ($data as $mesurement) {
            $tableData[] = [
                $mesurement->getName(),
                MesurementStatusDictionary::getStatus()[$mesurement->getStatus()],
            ];
        }

        $this->addTable($section, $tableData, true, self::TABLE_ORIENTATION_HORIZONTAL);
        $section->addPageBreak();
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
                    $mesurement->getCreator(),
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
}
