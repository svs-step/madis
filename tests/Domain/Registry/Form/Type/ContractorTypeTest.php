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

use App\Domain\Registry\Form\Type\ContractorType;
use App\Domain\Registry\Form\Type\Embeddable\AddressType;
use App\Domain\Registry\Model\Contractor;
use App\Tests\Utils\FormTypeHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContractorTypeTest extends FormTypeHelper
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, new ContractorType());
    }

    public function testBuildForm()
    {
        $builder = [
            'name'                       => TextType::class,
            'referent'                   => TextType::class,
            'contractualClausesVerified' => CheckboxType::class,
            'conform'                    => CheckboxType::class,
            'address'                    => AddressType::class,
        ];

        (new ContractorType())->buildForm($this->prophesizeBuilder($builder), []);
    }

    public function testConfigureOptions(): void
    {
        $defaults = [
            'data_class'        => Contractor::class,
            'validation_groups' => [
                'default',
                'contractor',
            ],
        ];

        $resolverProphecy = $this->prophesize(OptionsResolver::class);
        $resolverProphecy->setDefaults($defaults)->shouldBeCalled();

        (new ContractorType())->configureOptions($resolverProphecy->reveal());
    }
}
