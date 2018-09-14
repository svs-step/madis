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

use App\Application\Symfony\Security\UserProvider;
use App\Domain\Registry\Dictionary\TreatmentConcernedPeopleDictionary;
use App\Domain\Registry\Dictionary\TreatmentDataCategoryDictionary;
use App\Domain\Registry\Dictionary\TreatmentLegalBasisDictionary;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Shared\Converter;

class TreatmentGenerator extends AbstractGenerator implements ImpressionGeneratorInterface
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

    public function addGlobalOverview(Section $section, array $data): void
    {
        // GENERATE ALL DATA BEFORE WORD GENERATION IN ORDER TO AVOID SEVERAL LOOP
        $nbTreatments = \count($data);
        $overview     = [
            [
                'Nom',
                'Gestionnaire',
            ],
        ];
        $digitalisation = [
            'paper'       => 0,
            'onlyDigital' => 0,
            'digital'     => 0,
            'both'        => 0,
            'other'       => 0,
        ];
        $security = [
            'accessControl' => 0,
            'tracability'   => 0,
            'saving'        => 0,
            'update'        => 0,
        ];
        $completion = [
            '100' => 0,
            '80'  => 0,
        ];

        /*
         * @var Treatment
         */
        foreach ($data as $key => $treatment) {
            // Overview

            if (10 > $key) {
                $overview[] = [
                    $treatment->getName(),
                    $treatment->getManager() ?? $this->defaultReferent,
                ];
            }

            // Digitalisation
            if (!\is_null($treatment->getSoftware()) && $treatment->isPaperProcessing()) {
                ++$digitalisation['both'];
            } elseif (!\is_null($treatment->getSoftware())) {
                ++$digitalisation['onlyDigital'];
            } elseif ($treatment->isPaperProcessing()) {
                ++$digitalisation['paper'];
            } else {
                ++$digitalisation['other'];
            }

            // Security
            if (!\is_null($treatment->getSoftware())) {
                if ($treatment->getSecurityAccessControl()->isCheck()) {
                    ++$security['accessControl'];
                }
                if ($treatment->getSecurityTracability()->isCheck()) {
                    ++$security['tracability'];
                }
                if ($treatment->getSecuritySaving()->isCheck()) {
                    ++$security['saving'];
                }
                if ($treatment->getSecurityUpdate()->isCheck()) {
                    ++$security['update'];
                }
            }

            // Completion
            if (100 === $treatment->getCompletion()) {
                ++$completion['100'];
            } elseif (80 >= $treatment->getCompletion()) {
                ++$completion['80'];
            }
        }
        // Then aggregate
        $digitalisation['digital'] = $digitalisation['onlyDigital'] + $digitalisation['both'];

        $section->addTitle('Registre des traitements', 2);
        $section->addText('Une collecte des traitements a été réalisée. Chaque fiche de registre est établie sur une base de 20 critères. Les critères exigés par le règlement sont pris en compte.');

        $section->addTextBreak();
        $section->addText('Une version de ces traitements et à valeur de preuve figure en annexe.');

        $section->addTitle('Analyse du registre des traitements', 2);
        $section->addText("Il y a aujourd’hui {$nbTreatments} traitements de données à caractère personnel inventoriés");
        $section->addText("Sur les {$nbTreatments} traitements : ");
        $section->addListItem("{$completion['100']} sont complétés à plus de 100%");
        $section->addListItem("{$completion['80']} sont complétés à plus de 80%");

        $section->addTextBreak();
        $section->addText('Informatisation des traitements :');

        $categories = ['Uniquement papier', 'Complétement informatisé', 'Informatisé et papier', 'Non renseigné'];
        $chartData  = $digitalisation;
        unset($chartData['digital']); // Remove aggregate data which cumulate onlyDigital + both
        $chart      = $section->addChart(
            'pie',
            $categories,
            $chartData,
            [
                'height' => Converter::cmToEmu(11),
                'width'  => Converter::cmToEmu(15),
            ]
        );

        $section->addTextBreak();
        $section->addText("Sur les {$nbTreatments} traitements : ");
        $section->addListItem("{$digitalisation['paper']} sont uniquement papier");
        $section->addListItem("{$digitalisation['onlyDigital']} sont complétement informatisés");
        $section->addListItem("{$digitalisation['both']} sont informatisés et papier");
        if (0 < $digitalisation['other']) {
            $section->addListItem("{$digitalisation['other']} ne sont pas renseignés");
        }

        $section->addTitle('Sécurité de base des traitements informatisés', 2);
        $section->addText("Sur les {$digitalisation['digital']} traitements informatisés :");
        $section->addListItem("{$security['accessControl']} ont un contrôle d'accès");
        $section->addListItem("{$security['tracability']} ont une traçabilité");
        $section->addListItem("{$security['saving']} ont sont sauvegardés");
        $section->addListItem("{$security['update']} sont mis à jour");
    }

    public function addSyntheticView(Section $section, array $data, bool $forOverviewReport = false): void
    {
        // Break page for overview report
        if ($forOverviewReport) {
            $section->addPageBreak();
        }

        $section->addTitle('Liste des traitements', $forOverviewReport ? 2 : 1);

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

        // Don't break page if it's overview report
        if (!$forOverviewReport) {
            $section->addPageBreak();
        }
    }

    public function addDetailedView(Section $section, array $data): void
    {
        $section->addTitle('Détail des traitements', 1);

        foreach ($data as $key => $treatment) {
            if (0 !== $key) {
                $section->addPageBreak();
            }
            $section->addTitle($treatment->getName(), 2);

            $generalInformationsData = [
                [
                    'Finalités',
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
                    \is_string($treatment->getSoftware()) ? \htmlspecialchars($treatment->getSoftware()) : null,
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
