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
use App\Domain\User\Dictionary\ContactCivilityDictionary;
use PhpOffice\PhpWord\Element\Section;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

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
     * @var MaturityGenerator
     */
    protected $maturityGenerator;

    /**
     * @var MesurementGenerator
     */
    protected $mesurementGenerator;

    /**
     * @var RequestGenerator
     */
    protected $requestGenerator;

    /**
     * @var ViolationGenerator
     */
    protected $violationGenerator;

    public function __construct(
        UserProvider $userProvider,
        ParameterBagInterface $parameterBag,
        TreatmentGenerator $treatmentGenerator,
        ContractorGenerator $contractorGenerator,
        MaturityGenerator $maturityGenerator,
        MesurementGenerator $mesurementGenerator,
        RequestGenerator $requestGenerator,
        ViolationGenerator $violationGenerator
    ) {
        parent::__construct($userProvider, $parameterBag);
        $this->treatmentGenerator  = $treatmentGenerator;
        $this->contractorGenerator = $contractorGenerator;
        $this->maturityGenerator   = $maturityGenerator;
        $this->mesurementGenerator = $mesurementGenerator;
        $this->requestGenerator    = $requestGenerator;
        $this->violationGenerator  = $violationGenerator;
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
        $referentCivility = $referent->getCivility() ? ContactCivilityDictionary::getCivilities()[$referent->getCivility()] : null;
        $section->addListItem("{$referentCivility} {$referent->getFullName()}, {$referent->getJob()}");

        $itManager = $collectivity->getItManager();
        if ($collectivity->isDifferentItManager()) {
            $itManagerCivility = ContactCivilityDictionary::getCivilities()[$itManager->getCivility()];
            $section->addListItem("{$itManagerCivility} {$itManager->getFullName()}, {$itManager->getJob()}");
        }

        $dpo = $collectivity->getDpo();
        if ($collectivity->isDifferentDpo()) {
            $dpoCivility = ContactCivilityDictionary::getCivilities()[$dpo->getCivility()];
            $section->addListItem("{$dpoCivility} {$dpo->getFullName()}, {$dpo->getJob()}");
        }
    }

    public function generateRegistries(
        Section $section,
        array $treatments = [],
        array $contractors = [],
        array $requests = [],
        array $violations = []
    ): void {
        $collectivity = $this->userProvider->getAuthenticatedUser()->getCollectivity();

        $section->addTitle('Bilan des registres', 1);

        $section->addText("{$collectivity->getName()} recense 4 registres : ");
        $section->addListItem('Traitements');
        $section->addListItem('Sous-traitants');
        $section->addListItem('Demandes des personnes concernées');
        $section->addListItem('Violations de données');

        $this->treatmentGenerator->addGlobalOverview($section, $treatments);
        $this->contractorGenerator->addGlobalOverview($section, $contractors);
        $this->requestGenerator->addGlobalOverview($section, $requests);
        $this->violationGenerator->addGlobalOverview($section, $violations);
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

    public function generateAnnexeMention(Section $section, array $treatments = []): void
    {
        $section->addTitle('Liste des documents en annexe du bilan');
        $section->addListItem('La liste des traitements');

        $this->treatmentGenerator->addSyntheticView($section, $treatments, true);
    }
}
