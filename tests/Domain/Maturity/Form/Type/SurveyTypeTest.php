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

namespace App\Tests\Domain\Maturity\Form\Type;

use App\Domain\Maturity\Form\Type\SurveyType;
use App\Domain\Maturity\Model\Survey;
use App\Domain\Registry\Form\Type\ContractorType;
use App\Tests\Utils\FormTypeHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SurveyTypeTest extends FormTypeHelper
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, new ContractorType());
    }

    public function testBuildForm()
    {
        $builder = [
            'answers' => CollectionType::class,
        ];

        (new SurveyType())->buildForm($this->prophesizeBuilder($builder), []);
    }

    public function testConfigureOptions(): void
    {
        $defaults = [
            'data_class'        => Survey::class,
            'validation_groups' => [
                'default',
                'survey',
            ],
        ];

        $resolverProphecy = $this->prophesize(OptionsResolver::class);
        $resolverProphecy->setDefaults($defaults)->shouldBeCalled();

        (new SurveyType())->configureOptions($resolverProphecy->reveal());
    }
}
