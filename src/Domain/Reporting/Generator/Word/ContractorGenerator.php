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
use App\Domain\User\Dictionary\ContactCivilityDictionary;
use PhpOffice\PhpWord\Element\Section;

class ContractorGenerator extends AbstractGenerator implements ImpressionGeneratorInterface
{
    /**
     * Global overview : data to display for contractors in overview report.
     *
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
                'A adopté les éléments de sécurité nécessaires',
                'Tient à jour un registre des traitements',
                'Envoi des données hors UE',
            ],
        ];
        $nbContractors                = \count($data);
        $nbVerifiedContractualClauses = 0;
        $nbAdoptedSecurityFeatures    = 0;
        $nbMaintainsTreatmentRegister = 0;
        $nbSendingDataOutsideEu       = 0;

        // Make a loop to get all data. Make all data processing in one loop to avoid several loops
        foreach ($data as $contractor) {
            // Overview
            $overview[] = [
                $contractor->getName(),
                $contractor->getReferent() ?? $this->parameterBag->get('APP_DEFAULT_REFERENT'),
                $contractor->isContractualClausesVerified() ? 'Oui' : 'Non',
                $contractor->isAdoptedSecurityFeatures() ? 'Oui' : 'Non',
                $contractor->isMaintainsTreatmentRegister() ? 'Oui' : 'Non',
                $contractor->isSendingDataOutsideEu() ? 'Oui' : 'Non',
            ];

            // Verified contractual clauses
            if ($contractor->isContractualClausesVerified()) {
                ++$nbVerifiedContractualClauses;
            }
            // Adopted security features
            if ($contractor->isAdoptedSecurityFeatures()) {
                ++$nbAdoptedSecurityFeatures;
            }
            // Maintains treatment register
            if ($contractor->isMaintainsTreatmentRegister()) {
                ++$nbMaintainsTreatmentRegister;
            }
            // Sending data outside EU
            if ($contractor->isSendingDataOutsideEu()) {
                ++$nbSendingDataOutsideEu;
            }
        }

        $section->addTitle('Registre des sous-traitants', 2);

        if (empty($data)) {
            $section->addText('Il n’y a aucun sous-traitant identifié.');

            return;
        }

        $section->addText("Un recensement des sous-traitants gérants des données à caractère personnel de '{$collectivity}' a été effectué.");
        $section->addText("Il y a {$nbContractors} sous-traitants identifiés, les clauses contractuelles de {$nbVerifiedContractualClauses} d’entre eux ont été vérifiées. {$nbAdoptedSecurityFeatures} sous-traitants ont adopté les éléments de sécurité nécessaires. {$nbMaintainsTreatmentRegister} sous-traitants tiennent à jour un registre des traitements. {$nbSendingDataOutsideEu} sous-traitants envois des données hors UE.");
        $this->addTable($section, $overview, true, self::TABLE_ORIENTATION_HORIZONTAL);
    }

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
                'A adopté les éléments de sécurité nécessaires',
                'Tient à jour un registre des traitements',
                'Envoi des données hors UE',
            ],
        ];
        // Add content
        foreach ($data as $contractor) {
            $tableData[] = [
                $contractor->getName(),
                $contractor->getReferent() ?? $this->parameterBag->get('APP_DEFAULT_REFERENT'),
                $contractor->isContractualClausesVerified() ? 'Oui' : 'Non',
                $contractor->isAdoptedSecurityFeatures() ? 'Oui' : 'Non',
                $contractor->isMaintainsTreatmentRegister() ? 'Oui' : 'Non',
                $contractor->isSendingDataOutsideEu() ? 'Oui' : 'Non',
            ];
        }

        $this->addTable($section, $tableData, true, self::TABLE_ORIENTATION_HORIZONTAL);
        $section->addPageBreak();
    }

    public function addDetailedView(Section $section, array $data): void
    {
        $section->addTitle('Détail des sous-traitants', 1);

        /** @var Contractor $contractor */
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
                    'A adopté les éléments de sécurité nécessaires',
                    $contractor->isAdoptedSecurityFeatures() ? 'Oui' : 'Non',
                ],
                [
                    'Tient à jour un registre des traitements',
                    $contractor->isMaintainsTreatmentRegister() ? 'Oui' : 'Non',
                ],
                [
                    'Envoi des données hors UE',
                    $contractor->isSendingDataOutsideEu() ? 'Oui' : 'Non',
                ],
                [
                    'Autres informations',
                    $contractor->getOtherInformations(),
                ],
            ];

            $addressData = [
                [
                    'Responsable de traitement - Prénom',
                    $contractor->getLegalManager()->getFirstName(),
                ],
                [
                    'Responsable de traitement - Nom',
                    $contractor->getLegalManager()->getLastName(),
                ],
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
                    'Pays',
                    $contractor->getAddress()->getCountry(),
                ],
                [
                    'Email',
                    $contractor->getAddress()->getMail(),
                ],
                [
                    'Téléphone',
                    $contractor->getAddress()->getPhoneNumber(),
                ],
            ];

            $dpoData = [
                [
                    'Civilité',
                    !\is_null($contractor->getDpo()->getCivility()) ? ContactCivilityDictionary::getCivilities()[$contractor->getDpo()->getCivility()] : '',
                ],
                [
                    'Prénom',
                    $contractor->getDpo()->getFirstName(),
                ],
                [
                    'Nom',
                    $contractor->getDpo()->getLastName(),
                ],
                [
                    'Fonction',
                    $contractor->getDpo()->getJob(),
                ],
                [
                    'Email',
                    $contractor->getDpo()->getMail(),
                ],
                [
                    'Téléphone',
                    $contractor->getDpo()->getPhoneNumber(),
                ],
            ];

            $historyData = [
                [
                    'Date de création',
                    $this->getDate($contractor->getCreatedAt()),
                ],
                [
                    'Date de modification',
                    $this->getDate($contractor->getUpdatedAt()),
                ],
                [
                    'Modifié par',
                    $contractor->getUpdatedBy(),
                ],
            ];

            $section->addTitle('Informations générales', 3);
            $this->addTable($section, $generalInformationsData, true, self::TABLE_ORIENTATION_VERTICAL);

            $section->addTitle('Coordonnées', 3);
            $this->addTable($section, $addressData, true, self::TABLE_ORIENTATION_VERTICAL);

            $section->addTitle('DPD', 3);
            $this->addTable($section, $dpoData, true, self::TABLE_ORIENTATION_VERTICAL);

            $section->addTitle('Historique', 3);
            $this->addTable($section, $historyData, true, self::TABLE_ORIENTATION_VERTICAL);
        }
    }
}
