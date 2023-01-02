<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author Donovan Bourlard <donovan@awkan.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace App\Tests\Domain\User\Form\Type;

use App\Domain\User\Form\Type\ContactType;
use App\Domain\User\Model\Embeddable\Contact;
use App\Tests\Utils\FormTypeHelper;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactTypeTest extends FormTypeHelper
{
    protected Request $currentRequest;
    protected RequestStack $requestStack;

    public function setUp(): void
    {
        parent::setUp();
        $this->currentRequest = new Request(
            [], // GET parameters
            [], // POST parameters
            ['_route' => 'nothing'], // request attributes (parameters parsed from the PATH_INFO, ...)
            [], // COOKIE parameters
            [], // FILES parameters
            [], // SERVER parameters
            null // raw body data
        );
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->requestStack
            ->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn($this->currentRequest);
    }

    public function testInstanceOf(): void
    {
        $this->assertInstanceOf(AbstractType::class, new ContactType($this->requestStack));
    }

    public function testBuildForm(): void
    {
        $builder = [
            'civility'    => DictionaryType::class,
            'firstName'   => TextType::class,
            'lastName'    => TextType::class,
            'job'         => TextType::class,
            'mail'        => EmailType::class,
            'phoneNumber' => TextType::class,
        ];

        (new ContactType($this->requestStack))->buildForm($this->prophesizeBuilder($builder), []);
    }

    public function testConfigureOptions(): void
    {
        $defaults = [
            'data_class'        => Contact::class,
            'validation_groups' => 'default',
        ];

        $resolverProphecy = $this->prophesize(OptionsResolver::class);
        $resolverProphecy->setDefaults($defaults)->shouldBeCalled();

        (new ContactType($this->requestStack))->configureOptions($resolverProphecy->reveal());
    }
}
