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

namespace App\Tests\Domain\Registry\Form\Type;

use App\Domain\Registry\Form\Type\Embeddable\ComplexChoiceType;
use App\Domain\Registry\Form\Type\Embeddable\DelayType;
use App\Domain\Registry\Form\Type\TreatmentType;
use App\Domain\Registry\Model\Treatment;
use App\Domain\User\Model\Collectivity;
use App\Tests\Utils\FormTypeHelper;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class TreatmentTypeTest extends FormTypeHelper
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var TreatmentType
     */
    private $formType;

    protected function setUp()
    {
        $this->security = $this->prophesize(Security::class);

        $this->formType = new TreatmentType(
            $this->security->reveal()
        );
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, $this->formType);
    }

    public function testBuildForm()
    {
        $treatment      = new Treatment();
        $collectivity   = new Collectivity();
        $collectivity->setIsServicesEnabled(true);
        $treatment->setCollectivity($collectivity);

        $builder = [
            'public'                            => CheckboxType::class,
            'name'                              => TextType::class,
            'service'                           => EntityType::class,
            'goal'                              => TextareaType::class,
            'manager'                           => TextType::class,
            'software'                          => TextType::class,
            'paperProcessing'                   => CheckboxType::class,
            'legalBasis'                        => DictionaryType::class,
            'legalBasisJustification'           => TextareaType::class,
            'observation'                       => TextareaType::class,
            'concernedPeopleParticular'         => ComplexChoiceType::class,
            'concernedPeopleUser'               => ComplexChoiceType::class,
            'concernedPeopleAgent'              => ComplexChoiceType::class,
            'concernedPeopleElected'            => ComplexChoiceType::class,
            'concernedPeopleCompany'            => ComplexChoiceType::class,
            'concernedPeoplePartner'            => ComplexChoiceType::class,
            'concernedPeopleOther'              => ComplexChoiceType::class,
            'dataCategories'                    => EntityType::class,
            'dataCategoryOther'                 => TextareaType::class,
            'dataOrigin'                        => TextType::class,
            'recipientCategory'                 => TextareaType::class,
            'contractors'                       => EntityType::class,
            'delay'                             => DelayType::class,
            'securityAccessControl'             => ComplexChoiceType::class,
            'securityTracability'               => ComplexChoiceType::class,
            'securitySaving'                    => ComplexChoiceType::class,
            'securityUpdate'                    => ComplexChoiceType::class,
            'securityOther'                     => ComplexChoiceType::class,
            'systematicMonitoring'              => CheckboxType::class,
            'largeScaleCollection'              => CheckboxType::class,
            'vulnerablePeople'                  => CheckboxType::class,
            'dataCrossing'                      => CheckboxType::class,
            'evaluationOrRating'                => CheckboxType::class,
            'automatedDecisionsWithLegalEffect' => CheckboxType::class,
            'automaticExclusionService'         => CheckboxType::class,
            'innovativeUse'                     => CheckboxType::class,
            'active'                            => ChoiceType::class,
            'author'                            => DictionaryType::class,
            'coordonneesResponsableTraitement'  => TextareaType::class,
            'collectingMethod'                  => DictionaryType::class,
            'estimatedConcernedPeople'          => IntegerType::class,
            'securityEntitledPersons'           => CheckboxType::class,
            'securityOpenAccounts'              => CheckboxType::class,
            'securitySpecificitiesDelivered'    => CheckboxType::class,
            'ultimateFate'                      => DictionaryType::class,
        ];

        $this->formType->buildForm($this->prophesizeBuilder($builder), ['data' => $treatment]);
    }

    public function testConfigureOptions(): void
    {
        $defaults = [
            'data_class'        => Treatment::class,
            'validation_groups' => [
                'default',
                'treatment',
            ],
        ];

        $resolverProphecy = $this->prophesize(OptionsResolver::class);
        $resolverProphecy->setDefaults($defaults)->shouldBeCalled();

        $this->formType->configureOptions($resolverProphecy->reveal());
    }
}
