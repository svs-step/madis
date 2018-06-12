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

namespace App\Tests\Domain\User\Form\Type;

use App\Domain\User\Form\Type\AddressType;
use App\Domain\User\Form\Type\CollectivityType;
use App\Domain\User\Form\Type\ContactType;
use App\Tests\Utils\FormTypeHelper;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectivityTypeTest extends FormTypeHelper
{
    public function testInstanceOf(): void
    {
        $this->assertInstanceOf(AbstractType::class, new CollectivityType());
    }

    public function testBuildForm(): void
    {
        $builder = [
            'name'         => TextType::class,
            'shortName'    => TextType::class,
            'type'         => DictionaryType::class,
            'siren'        => NumberType::class,
            'active'       => ChoiceType::class,
            'website'      => UrlType::class,
            'address'      => AddressType::class,
            'legalManager' => ContactType::class,
            'referent'     => ContactType::class,
            'dpo'          => ContactType::class,
            'itManager'    => ContactType::class,
        ];

        (new CollectivityType())->buildForm($this->prophesizeBuilder($builder), []);
    }

    public function testConfigureOptions(): void
    {
        $defaults = [
            'validation_groups' => 'default',
        ];

        $resolverProphecy = $this->prophesize(OptionsResolver::class);
        $resolverProphecy->setDefaults($defaults)->shouldBeCalled();

        (new CollectivityType())->configureOptions($resolverProphecy->reveal());
    }
}
