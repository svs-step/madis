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
                'Statut',
            ],
        ];
        // Add content
        foreach ($data as $treatment) {
            $tableData[] = [
                $treatment->getName(),
                $treatment->getManager() ?? $this->defaultReferent,
                $treatment->isActive() ? 'Actif' : 'Inactif',
            ];
        }

        $this->addTable($section, $tableData, true, self::TABLE_ORIENTATION_HORIZONTAL);
    }

    public function generateDetails(PhpWord $document, array $data): void
    {
        /*
         * @var Treatment
         */
        foreach ($data as $treatment) {
            $section = $document->addSection();
            $section->addTitle($treatment->getName(), 2);

            $generalInformationsData = [
                [
                    'Finalité',
                    $treatment->getGoal(),
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
                    $treatment->getLegalBasis(),
                ],
                [
                    'Justification de la base légale',
                    $treatment->getLegalBasisJustification(),
                ],
            ];

            $detailsData = [
                [
                    'Personnes référentes',
                    \implode(', ', $treatment->getConcernedPeople()),
                ],
                [
                    'Délai de conservation',
                    $treatment->getDelay()->getComment() ?: "{$treatment->getDelay()->getNumber()} {$treatment->getDelay()->getPeriod()}",
                ],
            ];

            $categoryData = [
                [
                    'Catégorie de données',
                    $treatment->getDataCategory(),
                ],
                [
                    'Données à caractère sensible',
                    $treatment->isSensibleInformations() ? 'Oui' : 'Non',
                ],
            ];

            $goalData = [
                [
                    'Catégorie de destinataires',
                    $treatment->getRecipientCategory(),
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
                    'Chiffrement',
                    $treatment->getSecurityEncryption()->isCheck() ? 'Oui' : 'Non',
                    $treatment->getSecurityEncryption()->getComment(),
                ],
                [
                    'Autres',
                    $treatment->getSecurityOther()->isCheck() ? 'Oui' : 'Non',
                    $treatment->getSecurityOther()->getComment(),
                ],
            ];

            $historyData = [
                [
                    'Créateur',
                    $treatment->getCreator(),
                ],
                [
                    'Date de création',
                    $treatment->getCreatedAt()->format(self::DATE_TIME_FORMAT),
                ],
                [
                    'Dernière mise à jour',
                    $treatment->getUpdatedAt()->format(self::DATE_TIME_FORMAT),
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

            $section->addTitle('Historique', 3);
            $this->addTable($section, $historyData, true, self::TABLE_ORIENTATION_VERTICAL);
        }
    }
}
