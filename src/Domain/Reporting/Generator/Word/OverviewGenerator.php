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

    public function __construct(
        UserProvider $userProvider,
        TreatmentGenerator $treatmentGenerator,
        ContractorGenerator $contractorGenerator
    ) {
        parent::__construct($userProvider);
        $this->treatmentGenerator  = $treatmentGenerator;
        $this->contractorGenerator = $contractorGenerator;
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
        $section->addListItem('Foo');
        $section->addListItem('Bar');
        $section->addListItem('Baz');
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
}
