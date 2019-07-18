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

use App\Domain\Registry\Model\Contractor;
use PhpOffice\PhpWord\Element\Section;

class ContractorGenerator extends AbstractGenerator implements ImpressionGeneratorInterface
{
    /**
     * Global overview : data to display for contractors in overview report.
     *
     * @param Section      $section
     * @param Contractor[] $data
     */
    public function addGlobalOverview(Section $section, array $data): void
    {
        $collectivity = $this->userProvider->getAuthenticatedUser()->getCollectivity();

        $overview = [
            [
                'Nom',
                'Référent',
                'Clauses contractuelles vérifiées',
                'Conforme RGPD',
            ],
        ];
        $nbContractors                = \count($data);
        $nbVerifiedContractualClauses = 0;
        $nbConform                    = 0;

        // Make a loop to get all data. Make all data processing in one loop to avoid several loops
        foreach ($data as $contractor) {
            // Overview
            $overview[] = [
                $contractor->getName(),
                $contractor->getReferent() ?? $this->parameterBag->get('APP_DEFAULT_REFERENT'),
                $contractor->isContractualClausesVerified() ? 'Oui' : 'Non',
                $contractor->isConform() ? 'Oui' : 'Non',
            ];

            // Verified contractual clauses
            if ($contractor->isContractualClausesVerified()) {
                ++$nbVerifiedContractualClauses;
            }
            // Conform
            if ($contractor->isConform()) {
                ++$nbConform;
            }
        }

        $section->addTitle('Registre des sous-traitants', 2);
        $section->addText("Un recensement des sous-traitants gérants des données à caractère personnel de '{$collectivity}' a été effectué.");
        $section->addText("Il y a {$nbContractors} sous-traitants identifiés, les clauses contractuelles de {$nbVerifiedContractualClauses} d’entre eux ont été vérifiées. {$nbConform} sous-traitants sont conforme au RGPD.");
        $this->addTable($section, $overview, true, self::TABLE_ORIENTATION_HORIZONTAL);
    }

    /**
     * {@inheritdoc}
     */
    public function addSyntheticView(Section $section, array $data): void
    {
        $section->addTitle('Liste des sous-traitants', 1);

        // Table data
        // Add header
        $tableData = [
            [
                'Nom',
                'Référent',
                'Clauses contractuelles vérifiées',
                'Conforme RGPD',
            ],
        ];
        // Add content
        foreach ($data as $contractor) {
            $tableData[] = [
                $contractor->getName(),
                $contractor->getReferent() ?? $this->parameterBag->get('APP_DEFAULT_REFERENT'),
                $contractor->isContractualClausesVerified() ? 'Oui' : 'Non',
                $contractor->isConform() ? 'Oui' : 'Non',
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
        $section->addTitle('Détail des sous-traitants', 1);

        foreach ($data as $key => $contractor) {
            if (0 !== $key) {
                $section->addPageBreak();
            }
            $section->addTitle($contractor->getName(), 2);

            $generalInformationsData = [
                [
                    'Agent référent',
                    $contractor->getReferent() ?? $this->parameterBag->get('APP_DEFAULT_REFERENT'),
                ],
                [
                    'Clauses contractuelles vérifiées',
                    $contractor->isContractualClausesVerified() ? 'Oui' : 'Non',
                ],
                [
                    'Conforme RGPD',
                    $contractor->isConform() ? 'Oui' : 'Non',
                ],
                [
                    'Autres informations',
                    $contractor->getOtherInformations(),
                ],
            ];

            $addressData = [
                [
                    'Adresse',
                    [
                        $contractor->getAddress()->getLineOne(),
                        $contractor->getAddress()->getLineTwo(),
                        $contractor->getAddress()->getZipCode(),
                        $contractor->getAddress()->getCity(),
                    ],
                ],
                [
                    'Email',
                    $contractor->getAddress()->getMail(),
                ],
                [
                    'N° de téléphone',
                    $contractor->getAddress()->getPhoneNumber(),
                ],
            ];

            $historyData = [
                [
                    'Créateur',
                    $contractor->getCreator(),
                ],
                [
                    'Date de création',
                    $this->getDate($contractor->getCreatedAt()),
                ],
                [
                    'Dernière mise à jour',
                    $this->getDate($contractor->getUpdatedAt()),
                ],
            ];

            $section->addTitle('Informations générales', 3);
            $this->addTable($section, $generalInformationsData, true, self::TABLE_ORIENTATION_VERTICAL);

            $section->addTitle('Adresse', 3);
            $this->addTable($section, $addressData, true, self::TABLE_ORIENTATION_VERTICAL);

            $section->addTitle('Historique', 3);
            $this->addTable($section, $historyData, true, self::TABLE_ORIENTATION_VERTICAL);
        }
    }
}
