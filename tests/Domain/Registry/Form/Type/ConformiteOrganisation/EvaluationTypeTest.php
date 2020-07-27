<?php

namespace App\Tests\Domain\Registry\Form\Type\ConformiteOrganisation;

use App\Domain\Registry\Form\Type\ConformiteOrganisation\EvaluationType;
use App\Domain\Registry\Model\ConformiteOrganisation\Evaluation;
use App\Tests\Utils\FormTypeHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EvaluationTypeTest extends FormTypeHelper
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, (new EvaluationType()));
    }

    public function testBuildForm(): void
    {
        $builder = [
          'date'         => DateType::class,
          'participants' => CollectionType::class,
          'conformites'  => CollectionType::class,
          'save'         => SubmitType::class,
          'saveDraft'    => SubmitType::class,
        ];

        (new EvaluationType())->buildForm($this->prophesizeBuilder($builder), []);
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

        (new EvaluationType())->configureOptions($resolverProphecy->reveal());
    }
}
