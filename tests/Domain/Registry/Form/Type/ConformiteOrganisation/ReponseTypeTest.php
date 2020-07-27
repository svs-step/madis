<?php

namespace App\Tests\Domain\Registry\Form\Type\ConformiteOrganisation;

use App\Domain\Registry\Form\Type\ConformiteOrganisation\ReponseType;
use App\Domain\Registry\Model\ConformiteOrganisation\Reponse;
use App\Tests\Utils\FormTypeHelper;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReponseTypeTest extends FormTypeHelper
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, (new ReponseType()));
    }

    public function testBuildForm()
    {
        $builder = [
            'reponse'       => DictionaryType::class,
            'reponseRaison' => TextType::class,
        ];

        (new ReponseType())->buildForm($this->prophesizeBuilder($builder), []);
    }

    public function testConfigureOptions()
    {
        $defaults = [
            'data_class'        => Reponse::class,
            'validation_groups' => [
                'default',
            ],
        ];

        $resolverProphecy = $this->prophesize(OptionsResolver::class);
        $resolverProphecy->setDefaults($defaults)->shouldBeCalled();

        (new ReponseType())->configureOptions($resolverProphecy->reveal());
    }
}
