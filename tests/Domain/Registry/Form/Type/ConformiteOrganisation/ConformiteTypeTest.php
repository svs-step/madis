<?php

namespace App\Tests\Domain\Registry\Form\Type\ConformiteOrganisation;

use App\Domain\Registry\Form\Type\ConformiteOrganisation\ConformiteType;
use App\Domain\Registry\Model\ConformiteOrganisation\Conformite;
use App\Tests\Utils\FormTypeHelper;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ConformiteTypeTest extends FormTypeHelper
{
    /**
     * @var Security|ObjectProphecy
     */
    private $security;

    /**
     * @var ConformiteType
     */
    private $formType;

    public function setUp(): void
    {
        $this->security = $this->prophesize(Security::class);
        $this->formType = new ConformiteType($this->security->reveal());
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, $this->formType);
    }

    public function testBuildForm()
    {
        $builder = [
            'reponses' => CollectionType::class,
        ];

        $builderProphecy = $this->prophesizeBuilder($builder, false);

        $builderProphecy
            ->addEventListener(FormEvents::PRE_SET_DATA, Argument::any())
            ->shouldBeCalled()
        ;

        $this->formType->buildForm($builderProphecy->reveal(), []);
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
