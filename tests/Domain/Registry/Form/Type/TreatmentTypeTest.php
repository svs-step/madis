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

namespace App\Tests\Domain\Registry\Form\Type;

use App\Application\Symfony\Security\UserProvider;
use App\Domain\Registry\Form\Type\Embeddable\ComplexChoiceType;
use App\Domain\Registry\Form\Type\Embeddable\DelayType;
use App\Domain\Registry\Form\Type\TreatmentType;
use App\Domain\Registry\Model\Treatment;
use App\Tests\Utils\FormTypeHelper;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TreatmentTypeTest extends FormTypeHelper
{
    /**
     * @var UserProvider
     */
    private $userProviderProphecy;

    /**
     * @var TreatmentType
     */
    private $formType;

    protected function setUp()
    {
        $this->userProviderProphecy = $this->prophesize(UserProvider::class);

        $this->formType = new TreatmentType(
            $this->userProviderProphecy->reveal()
        );
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, $this->formType);
    }

    public function testBuildForm()
    {
        $builder = [
            'name'                    => TextType::class,
            'goal'                    => TextareaType::class,
            'manager'                 => TextType::class,
            'software'                => TextType::class,
            'paperProcessing'         => CheckboxType::class,
            'legalBasis'              => DictionaryType::class,
            'legalBasisJustification' => TextareaType::class,
            'concernedPeople'         => DictionaryType::class,
            'dataCategory'            => DictionaryType::class,
            'dataCategoryOther'       => TextareaType::class,
            'recipientCategory'       => TextareaType::class,
            'contractors'             => EntityType::class,
            'delay'                   => DelayType::class,
            'securityAccessControl'   => ComplexChoiceType::class,
            'securityTracability'     => ComplexChoiceType::class,
            'securitySaving'          => ComplexChoiceType::class,
            'securityUpdate'          => ComplexChoiceType::class,
            'securityOther'           => ComplexChoiceType::class,
            'systematicMonitoring'    => CheckboxType::class,
            'largeScaleCollection'    => CheckboxType::class,
            'vulnerablePeople'        => CheckboxType::class,
            'dataCrossing'            => CheckboxType::class,
            'active'                  => ChoiceType::class,
        ];

        $this->formType->buildForm($this->prophesizeBuilder($builder), []);
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
