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

use App\Domain\Registry\Dictionary\MesurementStatusDictionary;
use App\Domain\Registry\Model\Mesurement;
use PhpOffice\PhpWord\PhpWord;

class MesurementGenerator extends Generator
{
    public function generateOverview(PhpWord $document, array $data): void
    {
        $section = $document->addSection();

        $section->addTitle('Liste des mesures', 2);

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
    }

    public function generateDetails(PhpWord $document, array $data): void
    {
        /**
         * @var Mesurement
         */
        foreach ($data as $mesurement) {
            $section = $document->addSection();
            $section->addTitle($mesurement->getName(), 2);

            $generalInformationsData = [
                [
                    'Nom',
                    $mesurement->getName(),
                ],
                [
                    'Description',
                    $mesurement->getDescription() ? preg_split('/\R/', $mesurement->getDescription()) : null,
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
                    'Mise en place',
                    MesurementStatusDictionary::STATUS_NOT_APPLICABLE !== $mesurement->getStatus()
                    ? ($mesurement->isEtablished() ? 'Oui' : 'Non')
                    : 'Non applicable',
                ],
                [
                    'Planification',
                    MesurementStatusDictionary::STATUS_NOT_APPLICABLE !== $mesurement->getStatus()
                        ? ($mesurement->getPlanificationDate() ? $mesurement->getPlanificationDate()->format(self::DATE_TIME_FORMAT) : null)
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
                    $mesurement->getCreatedAt()->format(self::DATE_TIME_FORMAT),
                ],
                [
                    'Dernière mise à jour',
                    $mesurement->getUpdatedAt()->format(self::DATE_TIME_FORMAT),
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
