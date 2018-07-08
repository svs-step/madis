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

class ContractorGenerator extends Generator
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

        $section->addTitle('Liste des sous-traitants', 2);

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
                $contractor->getReferent() ?? $this->defaultReferent,
                $contractor->isContractualClausesVerified() ? 'Oui' : 'Non',
                $contractor->isConform() ? 'Oui' : 'Non',
            ];
        }

        $this->addTable($section, $tableData, true, self::TABLE_ORIENTATION_HORIZONTAL);
    }

    public function generateDetails(PhpWord $document, array $data): void
    {
        foreach ($data as $contractor) {
            $section = $document->addSection();
            $section->addTitle($contractor->getName(), 2);

            $generalInformationsData = [
                [
                    'Personne référente',
                    $contractor->getReferent() ?? $this->defaultReferent,
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
                    'Autres inforamtions',
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
                    'Numéro de téléphone',
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
                    $contractor->getCreatedAt()->format(self::DATE_TIME_FORMAT),
                ],
                [
                    'Dernière mise à jour',
                    $contractor->getUpdatedAt()->format(self::DATE_TIME_FORMAT),
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
