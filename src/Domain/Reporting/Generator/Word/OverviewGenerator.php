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
use App\Domain\Registry\Model\ConformiteOrganisation\Evaluation;
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

    /**
     * @var ConformiteTraitementGenerator
     */
    protected $conformiteTraitementGenerator;

    /**
     * @var ConformiteOrganisationGenerator
     */
    protected $conformiteOrganisationGenerator;

    public function __construct(
        UserProvider $userProvider,
        ParameterBagInterface $parameterBag,
        TreatmentGenerator $treatmentGenerator,
        ContractorGenerator $contractorGenerator,
        MaturityGenerator $maturityGenerator,
        MesurementGenerator $mesurementGenerator,
        RequestGenerator $requestGenerator,
        ViolationGenerator $violationGenerator,
        ConformiteTraitementGenerator $conformiteTraitementGenerator,
        ConformiteOrganisationGenerator $conformiteOrganisationGenerator
    ) {
        parent::__construct($userProvider, $parameterBag);
        $this->treatmentGenerator              = $treatmentGenerator;
        $this->contractorGenerator             = $contractorGenerator;
        $this->maturityGenerator               = $maturityGenerator;
        $this->mesurementGenerator             = $mesurementGenerator;
        $this->requestGenerator                = $requestGenerator;
        $this->violationGenerator              = $violationGenerator;
        $this->conformiteTraitementGenerator   = $conformiteTraitementGenerator;
        $this->conformiteOrganisationGenerator = $conformiteOrganisationGenerator;
    }

    public function generateObjectPart(Section $section): void
    {
        $collectivity = $this->userProvider->getAuthenticatedUser()->getCollectivity();

        $section->addTitle('Objet', 1);

        $section->addText(
            "Ce document constitue le bilan de gestion des données à caractère personnel de la structure '{$collectivity->getName()}'."
        );
    }

    public function generateOrganismIntroductionPart(Section $section): void
    {
        $collectivity = $this->userProvider->getAuthenticatedUser()->getCollectivity();

        $section->addTitle('Présentation de la structure', 1);

        $section->addTitle('Mission de la structure', 2);
        $section->addText(\ucfirst($collectivity->getName()) . ' est une structure territoriale.');

        $section->addTitle('Engagement de la direction', 2);

        if (!empty($collectivity->getReportingBlockManagementCommitment())) {
            \PhpOffice\PhpWord\Shared\Html::addHtml($section, $collectivity->getReportingBlockManagementCommitment(), false, false);
        } else {
            $section->addText("La direction de '{$collectivity->getName()}' a établi, documenté, mis en œuvre une politique de gestion des données à caractère personnel.");
            $section->addText('Cette politique décrit les mesures techniques et organisationnelles.');
            $section->addText("Cette politique a pour objectif de permettre à '{$collectivity->getName()}' de respecter dans le temps les exigences du RGPD et de pouvoir le démontrer.");
        }

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

        foreach ($collectivity->getComiteIlContacts() as $comiteIlContact) {
            $contact  = $comiteIlContact->getContact();
            $civility = ContactCivilityDictionary::getCivilities()[$contact->getCivility()];
            $section->addListItem("{$civility} {$contact->getFullName()}, {$contact->getJob()}");
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

    public function generateManagementSystemAndCompliance(
        Section $section,
        array $maturity = [],
        array $treatments = [],
        array $mesurements = [],
        Evaluation $evaluation = null
    ): void {
        $section->addTitle('Système de management des données à caractère personnel et conformité', 1);

        $this->maturityGenerator->addGlobalOverview($section, $maturity);
        $collectivity = $this->userProvider->getAuthenticatedUser()->getCollectivity();

        if ($collectivity->isHasModuleConformiteTraitement()) {
            $this->conformiteTraitementGenerator->addGlobalOverview($section, $treatments);
        }
        if ($collectivity->isHasModuleConformiteOrganisation()) {
            $this->conformiteOrganisationGenerator->addGlobalOverview($section, $evaluation);
        }
        $this->mesurementGenerator->addGlobalOverview($section, $mesurements);
    }

    public function generateContinuousImprovements(Section $section): void
    {
        $collectivity = $this->userProvider->getAuthenticatedUser()->getCollectivity();
        $section->addTitle("Principe d'amélioration continue", 1);
        $section->addText("Le système de management des DCP de '{$collectivity}' s’inscrit dans un principe d’amélioration continue. En conséquence :");
        if (!empty($collectivity->getReportingBlockManagementCommitment())) {
            \PhpOffice\PhpWord\Shared\Html::addHtml($section, $collectivity->getReportingBlockContinuousImprovement(), false, false);
        } else {
            $section->addListItem('Le référent opérationnel continue de mettre à jour le registre avec les éventuels nouveaux traitements effectués.');
            $section->addListItem('Le référent opérationnel continue de mettre à jour le registre avec les éventuels nouveaux sous-traitants.');
            $section->addListItem('Le comité génère un bilan chaque année et met en place les mesures correctives adéquates.');
        }
        $section->addText("Le responsable du traitement atteste avoir pris connaissance de l’ensemble des documents, approuve le bilan et s’engage à mettre en œuvre le plan d’action.");
        $section->addText('Signature du responsable du traitement');
        $section->addTextBreak(3);
        $section->addPageBreak();

    }

    public function generateAnnexeMention(Section $section, array $treatments = []): void
    {
        $section->addTitle('ANNEXES');
        $section->addListItem('La liste des traitements');
        $this->treatmentGenerator->TreatmentAnnexeList($section, $treatments);

        $this->treatmentGenerator->addSyntheticView($section, $treatments, true);
    }
}
