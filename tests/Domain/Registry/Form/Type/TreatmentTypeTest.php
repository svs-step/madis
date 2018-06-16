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

use App\Domain\Registry\Form\Type\TreatmentType;
use App\Domain\Registry\Model\Treatment;
use App\Tests\Utils\FormTypeHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TreatmentTypeTest extends FormTypeHelper
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, new TreatmentType());
    }

    public function testBuildForm()
    {
        $builder = [
            'name' => TextType::class,
        ];

        (new TreatmentType())->buildForm($this->prophesizeBuilder($builder), []);
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

        (new TreatmentType())->configureOptions($resolverProphecy->reveal());
    }
}
