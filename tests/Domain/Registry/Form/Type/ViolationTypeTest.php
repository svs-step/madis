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

use App\Domain\Registry\Form\Type\ViolationType;
use App\Domain\Registry\Model\Violation;
use App\Tests\Utils\FormTypeHelper;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ViolationTypeTest extends FormTypeHelper
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, new ViolationType());
    }

    public function testBuildForm()
    {
        $builder = [
            'date'                          => DateType::class,
            'inProgress'                    => CheckboxType::class,
            'violationNature'               => DictionaryType::class,
            'origins'                       => DictionaryType::class,
            'cause'                         => DictionaryType::class,
            'concernedDataNature'           => DictionaryType::class,
            'concernedPeopleCategories'     => DictionaryType::class,
            'nbAffectedRows'                => IntegerType::class,
            'nbAffectedPersons'             => IntegerType::class,
            'potentialImpactsNature'        => DictionaryType::class,
            'gravity'                       => DictionaryType::class,
            'communication'                 => DictionaryType::class,
            'communicationPrecision'        => TextareaType::class,
            'appliedMeasuresAfterViolation' => TextareaType::class,
            'notification'                  => DictionaryType::class,
            'notificationDetails'           => TextType::class,
            'comment'                       => TextareaType::class,
        ];

        (new ViolationType())->buildForm($this->prophesizeBuilder($builder), []);
    }

    public function testConfigureOptions(): void
    {
        $defaults = [
            'data_class'        => Violation::class,
            'validation_groups' => [
                'default',
                'violation',
            ],
        ];

        $resolverProphecy = $this->prophesize(OptionsResolver::class);
        $resolverProphecy->setDefaults($defaults)->shouldBeCalled();

        (new ViolationType())->configureOptions($resolverProphecy->reveal());
    }
}
