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

use App\Domain\Registry\Form\Type\Embeddable\RequestAnswerType;
use App\Domain\Registry\Form\Type\Embeddable\RequestApplicantType;
use App\Domain\Registry\Form\Type\Embeddable\RequestConcernedPeopleType;
use App\Domain\Registry\Form\Type\RequestType;
use App\Domain\Registry\Model\Request;
use App\Tests\Utils\FormTypeHelper;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequestTypeTest extends FormTypeHelper
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, new RequestType());
    }

    public function testBuildForm()
    {
        $builder = [
            'object'              => DictionaryType::class,
            'otherObject'         => TextType::class,
            'date'                => DateType::class,
            'reason'              => TextType::class,
            'applicant'           => RequestApplicantType::class,
            'concernedPeople'     => RequestConcernedPeopleType::class,
            'complete'            => CheckboxType::class,
            'legitimateApplicant' => CheckboxType::class,
            'legitimateRequest'   => CheckboxType::class,
            'answer'              => RequestAnswerType::class,
        ];

        (new RequestType())->buildForm($this->prophesizeBuilder($builder), []);
    }

    public function testConfigureOptions(): void
    {
        $defaults = [
            'data_class'        => Request::class,
            'validation_groups' => [
                'default',
                'request',
            ],
        ];

        $resolverProphecy = $this->prophesize(OptionsResolver::class);
        $resolverProphecy->setDefaults($defaults)->shouldBeCalled();

        (new RequestType())->configureOptions($resolverProphecy->reveal());
    }
}
