<?php

namespace App\Tests\Domain\Registry\Form\Type\ConformiteOrganisation;

use App\Application\Form\Extension\SanitizeTextFormType;
use App\Domain\Registry\Form\Type\ConformiteOrganisation\ParticipantType;
use App\Domain\Registry\Model\ConformiteOrganisation\Participant;
use App\Tests\Utils\FormTypeHelper;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantTypeTest extends FormTypeHelper
{
    use ProphecyTrait;

    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, new ParticipantType());
    }

    public function testBuildForm()
    {
        $builder = [
            'prenom'       => SanitizeTextFormType::class,
            'nomDeFamille' => SanitizeTextFormType::class,
            'civilite'     => DictionaryType::class,
            'fonction'     => SanitizeTextFormType::class,
        ];

        (new ParticipantType())->buildForm($this->prophesizeBuilder($builder), []);
    }

    public function testConfigureOptions(): void
    {
        $defaults = [
            'data_class'        => Participant::class,
            'validation_groups' => [
                'default',
            ],
        ];

        $resolverProphecy = $this->prophesize(OptionsResolver::class);
        $resolverProphecy->setDefaults($defaults)->shouldBeCalled();

        (new ParticipantType())->configureOptions($resolverProphecy->reveal());
    }
}
