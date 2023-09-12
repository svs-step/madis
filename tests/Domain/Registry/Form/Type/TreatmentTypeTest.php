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

use App\Application\Form\Extension\SanitizeTextAreaFormType;
use App\Application\Form\Extension\SanitizeTextFormType;
use App\Domain\Registry\Form\Type\Embeddable\ComplexChoiceType;
use App\Domain\Registry\Form\Type\TreatmentType;
use App\Domain\Registry\Model\Treatment;
use App\Domain\User\Model\Collectivity;
use App\Tests\Utils\FormTypeHelper;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;

class TreatmentTypeTest extends FormTypeHelper
{
    use ProphecyTrait;

    /**
     * @var Security
     */
    private $security;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authCheck;

    /**
     * @var TreatmentType
     */
    private $formType;

    protected function setUp(): void
    {
        $this->security  = $this->prophesize(Security::class);
        $this->authCheck = $this->prophesize(AuthorizationCheckerInterface::class);

        $this->formType = new TreatmentType(
            $this->security->reveal(),
            $this->authCheck->reveal()
        );
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, $this->formType);
    }

    public function testBuildFormWithoutToolsModule()
    {
        $treatment    = new Treatment();
        $collectivity = new Collectivity();
        $collectivity->setIsServicesEnabled(true);
        $treatment->setCollectivity($collectivity);

        $builder = [
            'public'                            => CheckboxType::class,
            'name'                              => SanitizeTextFormType::class,
            'exempt_AIPD'                       => CheckboxType::class,
            'service'                           => EntityType::class,
            'goal'                              => SanitizeTextAreaFormType::class,
            'manager'                           => SanitizeTextFormType::class,
            'software'                          => SanitizeTextFormType::class,
            'paperProcessing'                   => CheckboxType::class,
            'legalBasis'                        => DictionaryType::class,
            'legalBasisJustification'           => SanitizeTextAreaFormType::class,
            'observation'                       => SanitizeTextAreaFormType::class,
            'concernedPeopleParticular'         => ComplexChoiceType::class,
            'concernedPeopleUser'               => ComplexChoiceType::class,
            'concernedPeopleAgent'              => ComplexChoiceType::class,
            'concernedPeopleElected'            => ComplexChoiceType::class,
            'concernedPeopleCompany'            => ComplexChoiceType::class,
            'concernedPeoplePartner'            => ComplexChoiceType::class,
            'concernedPeopleUsager'             => ComplexChoiceType::class,
            'concernedPeopleOther'              => ComplexChoiceType::class,
            'dataCategories'                    => EntityType::class,
            'dataCategoryOther'                 => SanitizeTextAreaFormType::class,
            'dataOrigin'                        => SanitizeTextFormType::class,
            'recipientCategory'                 => SanitizeTextAreaFormType::class,
            'contractors'                       => EntityType::class,
            'shelfLifes'                        => CollectionType::class,
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
            'coordonneesResponsableTraitement'  => SanitizeTextAreaFormType::class,
            'collectingMethod'                  => DictionaryType::class,
            'estimatedConcernedPeople'          => IntegerType::class,
            'securityEntitledPersons'           => CheckboxType::class,
            'securityOpenAccounts'              => CheckboxType::class,
            'securitySpecificitiesDelivered'    => CheckboxType::class,
            'otherCollectingMethod'             => SanitizeTextFormType::class,
            'legalMentions'                     => CheckboxType::class,
            'consentRequest'                    => CheckboxType::class,
            'consentRequestFormat'              => SanitizeTextFormType::class,
            'updatedBy'                         => HiddenType::class,
            'statut'                            => HiddenType::class,
        ];

        $this->formType->buildForm($this->prophesizeBuilder($builder), ['data' => $treatment]);
    }

    public function testBuildFormWithToolsModule()
    {
        $treatment    = new Treatment();
        $collectivity = new Collectivity();
        $collectivity->setIsServicesEnabled(true);
        $collectivity->setHasModuleTools(true);
        $treatment->setCollectivity($collectivity);

        $builder = [
            'public'                            => CheckboxType::class,
            'name'                              => SanitizeTextFormType::class,
            'exempt_AIPD'                       => CheckboxType::class,
            'service'                           => EntityType::class,
            'goal'                              => SanitizeTextAreaFormType::class,
            'manager'                           => SanitizeTextFormType::class,
            'tools'                             => EntityType::class,
            'paperProcessing'                   => CheckboxType::class,
            'legalBasis'                        => DictionaryType::class,
            'legalBasisJustification'           => SanitizeTextAreaFormType::class,
            'observation'                       => SanitizeTextAreaFormType::class,
            'concernedPeopleParticular'         => ComplexChoiceType::class,
            'concernedPeopleUser'               => ComplexChoiceType::class,
            'concernedPeopleAgent'              => ComplexChoiceType::class,
            'concernedPeopleElected'            => ComplexChoiceType::class,
            'concernedPeopleCompany'            => ComplexChoiceType::class,
            'concernedPeoplePartner'            => ComplexChoiceType::class,
            'concernedPeopleUsager'             => ComplexChoiceType::class,
            'concernedPeopleOther'              => ComplexChoiceType::class,
            'dataCategories'                    => EntityType::class,
            'dataCategoryOther'                 => SanitizeTextAreaFormType::class,
            'dataOrigin'                        => SanitizeTextFormType::class,
            'recipientCategory'                 => SanitizeTextAreaFormType::class,
            'contractors'                       => EntityType::class,
            'shelfLifes'                        => CollectionType::class,
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
            'coordonneesResponsableTraitement'  => SanitizeTextAreaFormType::class,
            'collectingMethod'                  => DictionaryType::class,
            'estimatedConcernedPeople'          => IntegerType::class,
            'securityEntitledPersons'           => CheckboxType::class,
            'securityOpenAccounts'              => CheckboxType::class,
            'securitySpecificitiesDelivered'    => CheckboxType::class,
            'otherCollectingMethod'             => SanitizeTextFormType::class,
            'legalMentions'                     => CheckboxType::class,
            'consentRequest'                    => CheckboxType::class,
            'consentRequestFormat'              => SanitizeTextFormType::class,
            'updatedBy'                         => HiddenType::class,
            'statut'                            => HiddenType::class,
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
