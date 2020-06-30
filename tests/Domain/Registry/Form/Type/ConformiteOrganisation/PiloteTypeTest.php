<?php

namespace App\Tests\Domain\Registry\Form\Type\ConformiteOrganisation;

use App\Domain\Registry\Form\Type\ConformiteOrganisation\PiloteType;
use App\Domain\Registry\Model\ConformiteOrganisation\Conformite;
use App\Tests\Utils\FormTypeHelper;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class PiloteTypeTest extends FormTypeHelper
{
    /**
     * @var Security|ObjectProphecy
     */
    private $security;

    /**
     * @var PiloteType
     */
    private $formType;

    public function setUp()
    {
        $this->security = $this->prophesize(Security::class);
        $this->formType = new PiloteType($this->security->reveal());
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, $this->formType);
    }

    public function testBuildForm()
    {
        $builder = [
            'pilote' => TextType::class,
        ];
        $this->formType->buildForm($this->prophesizeBuilder($builder), []);
    }

    public function testConfigureOptions(): void
    {
        $defaults = [
            'data_class'        => Conformite::class,
            'validation_groups' => [
                'default',
            ],
        ];

        $resolverProphecy = $this->prophesize(OptionsResolver::class);
        $resolverProphecy->setDefaults($defaults)->shouldBeCalled();

        $this->formType->configureOptions($resolverProphecy->reveal());
    }
}
