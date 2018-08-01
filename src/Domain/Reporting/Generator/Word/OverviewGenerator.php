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
use App\Domain\User\Dictionary\ContactCivilityDictionary;
use PhpOffice\PhpWord\Element\Section;

class OverviewGenerator extends AbstractGenerator
{
    /**
     * @var TreatmentGenerator
     */
    protected $treatmentGenerator;

    /**
     * @var ContractorGenerator
     */
    protected $contractorGenerator;

    /**
     * @var MesurementGenerator
     */
    protected $mesurementGenerator;

    /**
     * @var MaturityGenerator
     */
    protected $maturityGenerator;

    public function __construct(
        UserProvider $userProvider,
        TreatmentGenerator $treatmentGenerator,
        ContractorGenerator $contractorGenerator,
        MesurementGenerator $mesurementGenerator,
        MaturityGenerator $maturityGenerator
    ) {
        parent::__construct($userProvider);
        $this->treatmentGenerator  = $treatmentGenerator;
        $this->contractorGenerator = $contractorGenerator;
        $this->mesurementGenerator = $mesurementGenerator;
        $this->maturityGenerator   = $maturityGenerator;
    }

    public function generateObjectPart(Section $section): void
    {
        $collectivity = $this->userProvider->getAuthenticatedUser()->getCollectivity();

        $section->addTitle('Objet', 1);

        $section->addText(
            "Ce document constitue le bilan de gestion des données à caractère personnel de la collectivité '{$collectivity->getName()}'."
        );
    }

    public function generateOrganismIntroductionPart(Section $section): void
    {
        $collectivity = $this->userProvider->getAuthenticatedUser()->getCollectivity();

        $section->addTitle('Présentation de l\'organisme', 1);

        $section->addTitle('Mission de l\'organisme', 2);
        $section->addText(\ucfirst($collectivity->getName()) . ' est une collectivité territoriale.');

        $section->addTitle('Engagement de la direction', 2);
        $section->addText("La direction de '{$collectivity->getName()}' a établi, documenté, mis en œuvre une politique de gestion des données à caractère personnel.");
        $section->addText('Cette politique décrit les mesures techniques et organisationnelles.');
        $section->addText("Cette politique a pour objectif de permettre à '{$collectivity->getName()}' de respecter dans le temps les exigences du RGPD et de pouvoir le démontrer.");

        $section->addTitle('Composition du comité Informatique et Liberté', 2);

        $legalManager         = $collectivity->getLegalManager();
        $legalManagerCivility = ContactCivilityDictionary::getCivilities()[$legalManager->getCivility()];
        $section->addListItem("{$legalManagerCivility} {$legalManager->getFullName()}, {$legalManager->getJob()}");

        $referent         = $collectivity->getReferent();
        $referentCivility = ContactCivilityDictionary::getCivilities()[$referent->getCivility()];
        $section->addListItem("{$referentCivility} {$referent->getFullName()}, {$referent->getJob()}");

        $itManager = $collectivity->getItManager();
        if (!\is_null($itManager->getFirstName()) && !\is_null($itManager->getLastName())) {
            $itManagerCivility = ContactCivilityDictionary::getCivilities()[$itManager->getCivility()];
            $section->addListItem("{$itManagerCivility} {$itManager->getFullName()}, {$itManager->getJob()}");
        }
    }

    public function generateRegistries(Section $section, array $treatments = [], array $contractors = []): void
    {
        $collectivity = $this->userProvider->getAuthenticatedUser()->getCollectivity();

        $section->addTitle('Bilan des registres', 1);

        $section->addText("{$collectivity->getName()} recense 2 registres : ");
        $section->addListItem('Traitements');
        $section->addListItem('Sous-traitants');

        $this->treatmentGenerator->addGlobalOverview($section, $treatments);
        $this->contractorGenerator->addGlobalOverview($section, $contractors);
    }

    public function generateManagementSystemAndCompliance(Section $section, array $maturity = [], array $mesurements = []): void
    {
        $section->addTitle('Système de management des DCP et conformité', 1);

        $this->maturityGenerator->addGlobalOverview($section, $maturity);
        $this->mesurementGenerator->addGlobalOverview($section, $mesurements);
    }

    public function generateContinuousImprovements(Section $section): void
    {
        $collectivity = $this->userProvider->getAuthenticatedUser()->getCollectivity();
        $section->addTitle("Principe d'amélioration continue", 1);
        $section->addText("Le système de management des DCP de '{$collectivity}' s’inscrit dans un principe d’amélioration continue. En conséquence :");
        $section->addListItem('Le référent opérationnel continue de mettre à jour le registre avec les éventuels nouveaux traitements effectués.');
        $section->addListItem('Le référent opérationnel continue de mettre à jour le registre avec les éventuels nouveaux sous-traitants.');
        $section->addListItem('Le comité génère un bilan chaque année et met en place les mesures correctives adéquates.');
    }

    public function generateAnnexeMention(Section $section): void
    {
        $section->addTitle('Liste des documents en annexe du bilan');
        $section->addListItem('La liste des traitements');
        $section->addListItem("L'indice de maturité");
    }
}
