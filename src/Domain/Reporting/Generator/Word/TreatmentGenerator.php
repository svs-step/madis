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

use App\Application\Symfony\Security\UserProvider;
use App\Domain\Registry\Dictionary\TreatmentConcernedPeopleDictionary;
use App\Domain\Registry\Dictionary\TreatmentDataCategoryDictionary;
use App\Domain\Registry\Dictionary\TreatmentLegalBasisDictionary;
use App\Domain\Registry\Model\Treatment;
use PhpOffice\PhpWord\PhpWord;

class TreatmentGenerator extends Generator
{
    /**
     * @var string
     */
    private $defaultReferent;

    public function __construct(
        UserProvider $userProvider,
        string $defaultReferent
    ) {
        parent::__construct($userProvider);
        $this->defaultReferent = $defaultReferent;
    }

    public function generateOverview(PhpWord $document, array $data): void
    {
        $section = $document->addSection();

        $section->addTitle('Liste des traitements', 2);

        // Table data
        // Add header
        $tableData = [
            [
                'Nom',
                'Gestionnaire',
            ],
        ];
        // Add content
        foreach ($data as $treatment) {
            $tableData[] = [
                $treatment->getName(),
                $treatment->getManager() ?? $this->defaultReferent,
            ];
        }

        $this->addTable($section, $tableData, true, self::TABLE_ORIENTATION_HORIZONTAL);
    }

    public function generateDetails(PhpWord $document, array $data): void
    {
        /*
         * @var Treatment $treatment
         */
        foreach ($data as $treatment) {
            $section = $document->addSection();
            $section->addTitle($treatment->getName(), 2);

            $generalInformationsData = [
                [
                    'Finalité',
                    $treatment->getGoal() ? \preg_split('/\R/', $treatment->getGoal()) : null,
                ],
                [
                    'Gestionnaire',
                    $treatment->getManager() ?? $this->defaultReferent,
                ],
                [
                    'Statut',
                    $treatment->isActive() ? 'Actif' : 'Inactif',
                ],
                [
                    'Base légale',
                    TreatmentLegalBasisDictionary::getBasis()[$treatment->getLegalBasis()],
                ],
                [
                    'Justification de la base légale',
                    $treatment->getLegalBasisJustification() ? \preg_split('/\R/', $treatment->getLegalBasisJustification()) : null,
                ],
            ];

            $detailsData = [
                [
                    'Personnes référentes',
                    // Values are added below
                ],
                [
                    'Logiciel',
                    $treatment->getSoftware(),
                ],
                [
                    'Gestion papier',
                    $treatment->isPaperProcessing() ? 'Oui' : 'Non',
                ],
                [
                    'Délai de conservation',
                    $treatment->getDelay()->getComment()
                        ? \preg_split('/\R/', $treatment->getDelay()->getComment())
                        : "{$treatment->getDelay()->getNumber()} {$treatment->getDelay()->getPeriod()}",
                ],
            ];
            // Add Concerned people
            $concernedPeople = [];
            foreach ($treatment->getConcernedPeople() as $people) {
                $concernedPeople[] = TreatmentConcernedPeopleDictionary::getTypes()[$people];
            }
            $detailsData[0][] = $concernedPeople;

            $categoryData = [
                [
                    'Catégorie de données',
                    // Values are added below
                ],
                [
                    'Autres catégories',
                    $treatment->getDataCategoryOther(),
                ],
                [
                    'Données à caractère sensible',
                    $treatment->isSensibleInformations() ? 'Oui' : 'Non',
                ],
            ];
            // Add data categories
            $dataCategories = [];
            foreach ($treatment->getDataCategory() as $category) {
                $dataCategories[] = TreatmentDataCategoryDictionary::getCategories()[$category];
            }
            $categoryData[0][] = $dataCategories;

            $goalData = [
                [
                    'Catégorie de destinataires',
                    $treatment->getRecipientCategory() ? \preg_split('/\R/', $treatment->getRecipientCategory()) : null,
                ],
                [
                    'Sous-traitant(s)',
                    \implode(', ', $treatment->getContractors()->toArray()),
                ],
            ];

            $securityData = [
                [
                    'Contrôle d\'accès',
                    $treatment->getSecurityAccessControl()->isCheck() ? 'Oui' : 'Non',
                    $treatment->getSecurityAccessControl()->getComment(),
                ],
                [
                    'Tracabilité',
                    $treatment->getSecurityTracability()->isCheck() ? 'Oui' : 'Non',
                    $treatment->getSecurityTracability()->getComment(),
                ],
                [
                    'Sauvegarde',
                    $treatment->getSecuritySaving()->isCheck() ? 'Oui' : 'Non',
                    $treatment->getSecuritySaving()->getComment(),
                ],
                [
                    'Mise à jour',
                    $treatment->getSecurityUpdate()->isCheck() ? 'Oui' : 'Non',
                    $treatment->getSecurityUpdate()->getComment(),
                ],
                [
                    'Autres',
                    $treatment->getSecurityOther()->isCheck() ? 'Oui' : 'Non',
                    $treatment->getSecurityOther()->getComment(),
                ],
            ];

            /**
             * @var Treatment
             */
            $specificData = [
                [
                    'Surveillance systématique',
                    $treatment->isSystematicMonitoring() ? 'Oui' : 'Non',
                ],
                [
                    'Collecte à large échelle',
                    $treatment->isLargeScaleCollection() ? 'Oui' : 'Non',
                ],
                [
                    'Personnes vulnérables',
                    $treatment->isVulnerablePeople() ? 'Oui' : 'Non',
                ],
                [
                    'Croisement de données',
                    $treatment->isDataCrossing() ? 'Oui' : 'Non',
                ],
            ];

            $historyData = [
                [
                    'Créateur',
                    $treatment->getCreator(),
                ],
                [
                    'Date de création',
                    $this->getDate($treatment->getCreatedAt()),
                ],
                [
                    'Dernière mise à jour',
                    $this->getDate($treatment->getUpdatedAt()),
                ],
            ];

            $section->addTitle('Informations générales', 3);
            $this->addTable($section, $generalInformationsData, true, self::TABLE_ORIENTATION_VERTICAL);

            $section->addTitle('Détails', 3);
            $this->addTable($section, $detailsData, true, self::TABLE_ORIENTATION_VERTICAL);

            $section->addTitle('Catégorie de données', 3);
            $this->addTable($section, $categoryData, true, self::TABLE_ORIENTATION_VERTICAL);

            $section->addTitle('Destination', 3);
            $this->addTable($section, $goalData, true, self::TABLE_ORIENTATION_VERTICAL);

            $section->addTitle('Mesures de sécurité', 3);
            $this->addTable($section, $securityData, true, self::TABLE_ORIENTATION_VERTICAL);

            $section->addTitle('Traitement spécifique', 3);
            $this->addTable($section, $specificData, true, self::TABLE_ORIENTATION_VERTICAL);

            $section->addTitle('Historique', 3);
            $this->addTable($section, $historyData, true, self::TABLE_ORIENTATION_VERTICAL);
        }
    }
}
