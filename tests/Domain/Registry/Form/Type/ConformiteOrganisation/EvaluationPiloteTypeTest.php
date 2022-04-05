<?php

namespace App\Tests\Domain\Registry\Form\Type\ConformiteOrganisation;

use App\Domain\Registry\Form\Type\ConformiteOrganisation\EvaluationPiloteType;
use App\Domain\Registry\Model\ConformiteOrganisation\Evaluation;
use App\Tests\Utils\FormTypeHelper;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class EvaluationPiloteTypeTest extends FormTypeHelper
{
    /**
     * @var Security|ObjectProphecy
     */
    private $security;

    /**
     * @var EvaluationPiloteType
     */
    private $formType;

    public function setUp(): void
    {
        $this->security = $this->prophesize(Security::class);
        $this->formType = new EvaluationPiloteType($this->security->reveal());
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, $this->formType);
    }

    public function testBuildForm()
    {
        $builder = [
            'conformites' => CollectionType::class,
        ];
        $this->formType->buildForm($this->prophesizeBuilder($builder), []);
    }

    public function testConfigureOptions(): void
    {
        $defaults = [
            'data_class'        => Evaluation::class,
            'validation_groups' => [
                'default',
            ],
        ];

        $resolverProphecy = $this->prophesize(OptionsResolver::class);
        $resolverProphecy->setDefaults($defaults)->shouldBeCalled();

        $this->formType->configureOptions($resolverProphecy->reveal());
    }
}
