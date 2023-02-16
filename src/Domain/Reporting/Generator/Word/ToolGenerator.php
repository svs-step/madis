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

use App\Application\Symfony\Security\UserProvider;
use App\Domain\Registry\Dictionary\ToolTypeDictionary;
use App\Domain\Registry\Model\Tool;
use App\Domain\Registry\Model\Treatment;
use PhpOffice\PhpWord\Element\Section;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ToolGenerator extends AbstractGenerator implements ImpressionGeneratorInterface
{
    protected TranslatorInterface $translator;

    public function __construct(
        UserProvider $userProvider,
        ParameterBagInterface $parameterBag,
        TranslatorInterface $translator
    ) {
        parent::__construct(
            $userProvider,
            $parameterBag
        );

        $this->translator = $translator;
    }

    /**
     * Global overview : Information to display for treatment in overview report.
     */
    public function addGlobalOverview(Section $section, array $data): void
    {
        // GENERATE ALL DATA BEFORE WORD GENERATION IN ORDER TO AVOID SEVERAL LOOP
        $nbTools  = \count($data);
        $overview = [
            [
                'Nom',
                'Gestionnaire',
            ],
        ];

        /*
         * @var Treatment
         */
        foreach ($data as $key => $tool) {
            /* @var Tool $tool */
            // Overview

            if (10 > $key) {
                $overview[] = [
                    $tool->getName(),
                    $tool->getManager() ?? $this->parameterBag->get('APP_DEFAULT_REFERENT'),
                ];
            }
        }

        $section->addTitle('Registre des logiciels et supports', 2);

        if (empty($data)) {
            $section->addText('Il n’y a aucun logiciel ou support identifié.');

            return;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addSyntheticView(Section $section, array $data, bool $forOverviewReport = false): void
    {
        // Break page for overview report
        if ($forOverviewReport) {
            $section->addPageBreak();
        }

        $section->addTitle('Liste des logiciels et supports', $forOverviewReport ? 2 : 1);

        // Table data
        // Add header
        $tableData = [
            [
                'Nom',
                'Type',
                'Éditeur',
                'Sous-traitants',
            ],
        ];
        // Add content
        foreach ($data as $tool) {
            /* @var Tool $tool */
            $tableData[] = [
                $tool->getName(),
                ToolTypeDictionary::getTypes()[$tool->getType()],
                $tool->getEditor(),
                Tool::generateLinkedDataColumn($tool->getContractors()),
            ];
        }

        $this->addTable($section, $tableData, true, self::TABLE_ORIENTATION_HORIZONTAL);

        // Don't break page if it's overview report
        if (!$forOverviewReport) {
            $section->addPageBreak();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addDetailedView(Section $section, array $data): void
    {
        $section->addTitle('Détail des logiciels et supports', 1);

        foreach ($data as $key => $tool) {
            /* @var Tool $tool */
            if (0 !== $key) {
                $section->addPageBreak();
            }
            $section->addTitle($tool->getName(), 2);

            $generalInformationsData = [
                [
                    'Type',
                    $tool->getType(),
                ],
                [
                    'Description',
                    $tool->getDescription(),
                ],
                [
                    'Éditeur',
                    $tool->getEditor(),
                ],
                [
                    'Sous-traitants',
                    Tool::generateLinkedDataColumn($tool->getContractors()),
                ],
                [
                    'Date de mise en production',
                    $this->getDate($tool->getProdDate()),
                ],
                [
                    'Pays d\'hébergement ou de stockage',
                    $this->translator->trans($tool->getCountryType()),
                ],
            ];

            if (Tool::COUNTRY_FRANCE !== $tool->getCountryType()) {
                $generalInformationsData[] = [
                    'Nom du pays',
                    $tool->getCountryName(),
                ];
            }

            if (Tool::COUNTRY_OTHER === $tool->getCountryType()) {
                $generalInformationsData[] = [
                    'Garanties pour le transfert',
                    $tool->getCountryGuarantees(),
                ];
            }

            $generalInformationsData[] = [
                'Personne en charge',
                $tool->getManager(),
            ];

            $securityData = [
                [
                    'Archivage',
                    $tool->getArchival()->isCheck() ? 'Oui' : 'Non',
                    $tool->getArchival()->getComment(),
                ],
                [
                    'Chiffrement',
                    $tool->getEncrypted()->isCheck() ? 'Oui' : 'Non',
                    $tool->getEncrypted()->getComment(),
                ],
                [
                    'Contrôle d\'accès',
                    $tool->getAccessControl()->isCheck() ? 'Oui' : 'Non',
                    $tool->getAccessControl()->getComment(),
                ],
                [
                    'Mise à jour',
                    $tool->getUpdate()->isCheck() ? 'Oui' : 'Non',
                    $tool->getUpdate()->getComment(),
                ],
                [
                    'Sauvegarde',
                    $tool->getBackup()->isCheck() ? 'Oui' : 'Non',
                    $tool->getBackup()->getComment(),
                ],
                [
                    'Suppression',
                    $tool->getDeletion()->isCheck() ? 'Oui' : 'Non',
                    $tool->getDeletion()->getComment(),
                ],
                [
                    'Traçabilité',
                    $tool->getTracking()->isCheck() ? 'Oui' : 'Non',
                    $tool->getTracking()->getComment(),
                ],
                [
                    'Zone de commentaire libre',
                    $tool->getHasComment()->isCheck() ? 'Oui' : 'Non',
                    $tool->getHasComment()->getComment(),
                ],
                [
                    'Autres',
                    $tool->getOther()->isCheck() ? 'Oui' : 'Non',
                    $tool->getOther()->getComment(),
                ],
            ];

            $historyData = [
                [
                    'Créateur',
                    strval($tool->getCreator()),
                ],
                [
                    'Date de création',
                    $this->getDate($tool->getCreatedAt()),
                ],
                [
                    'Dernière mise à jour',
                    $this->getDate($tool->getUpdatedAt()),
                ],
            ];

            $section->addTitle('Informations générales', 3);
            $this->addTable($section, $generalInformationsData, true, self::TABLE_ORIENTATION_VERTICAL);
            $section->addTitle('Mesures de sécurité et confidentialité', 3);
            $this->addTable($section, $securityData, true, self::TABLE_ORIENTATION_VERTICAL);
            $section->addTitle('Historique', 3);
            $this->addTable($section, $historyData, true, self::TABLE_ORIENTATION_VERTICAL);
        }
    }
}
