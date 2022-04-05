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

namespace App\Tests\Application\Controller;

use App\Application\Controller\ControllerHelper;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class ControllerHelperTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var Environment
     */
    private $twigProphecy;

    /**
     * @var RouterInterface
     */
    private $routerProphecy;

    /**
     * @var FormFactoryInterface
     */
    private $formFactoryProphecy;

    /**
     * @var FlashBagInterface
     */
    private $flashBagProphecy;

    /**
     * @var TranslatorInterface
     */
    private $translatorProphecy;

    /**
     * @var ControllerHelper
     */
    private $helper;

    public function setUp(): void
    {
        $this->twigProphecy        = $this->prophesize(Environment::class);
        $this->routerProphecy      = $this->prophesize(RouterInterface::class);
        $this->formFactoryProphecy = $this->prophesize(FormFactoryInterface::class);
        $this->flashBagProphecy    = $this->prophesize(FlashBagInterface::class);
        $this->translatorProphecy  = $this->prophesize(TranslatorInterface::class);

        $this->helper = new ControllerHelper(
            $this->twigProphecy->reveal(),
            $this->routerProphecy->reveal(),
            $this->formFactoryProphecy->reveal(),
            $this->flashBagProphecy->reveal(),
            $this->translatorProphecy->reveal()
        );
    }

    /**
     * Test render.
     */
    public function testRender()
    {
        $name = 'name';

        $this->twigProphecy->render($name, [])->shouldBeCalled()->willReturn('dummyContent');

        $this->assertInstanceOf(Response::class, $this->helper->render($name));
    }

    /**
     * Test render
     * Complete whole params.
     */
    public function testRenderWithWholeParams()
    {
        $name    = 'name';
        $context = ['foo' => 'bar'];

        $this->twigProphecy->render($name, $context)->shouldBeCalled()->willReturn('dummyContent');

        $this->assertInstanceOf(Response::class, $this->helper->render($name, $context));
    }

    /**
     * Test redirectToRoute.
     */
    public function testRedirectToRoute()
    {
        $route = 'routeName';

        $this->routerProphecy
            ->generate($route, [], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->shouldBeCalled()
            ->willReturn('http://dummyUrl')
        ;

        $response = $this->helper->redirectToRoute($route);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * Test redirectToRoute
     * Complete whole params.
     */
    public function testRedirectToRouteWithWholeParams()
    {
        $route  = 'routeName';
        $params = ['foo' => 'bar'];
        $status = 301;

        $this->routerProphecy
            ->generate($route, $params, UrlGeneratorInterface::ABSOLUTE_PATH)
            ->shouldBeCalled()
            ->willReturn('http://dummyUrl')
        ;

        $response = $this->helper->redirectToRoute($route, $params, $status);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals($status, $response->getStatusCode());
    }

    /**
     * Test addFlash.
     */
    public function testAddFlash()
    {
        $type    = 'type';
        $message = 'message';

        $this->flashBagProphecy->add($type, $message)->shouldBeCalled();

        $this->helper->addFlash($type, $message);
    }

    /**
     * Test trans.
     */
    public function testTrans()
    {
        $key           = 'keyToTranslate';
        $translatedKey = 'translatedKey';

        $this->translatorProphecy->trans($key, [], null)->shouldBeCalled()->willReturn($translatedKey);

        $this->assertEquals($translatedKey, $this->helper->trans($key));
    }

    /**
     * Test trans.
     * Complete whole params.
     */
    public function testTransWithWholeParams()
    {
        $key           = 'keyToTranslate';
        $params        = ['foo' => 'bar'];
        $domain        = 'foo';
        $translatedKey = 'translatedKey';

        $this->translatorProphecy->trans($key, $params, $domain)->shouldBeCalled()->willReturn($translatedKey);

        $this->assertEquals($translatedKey, $this->helper->trans($key, $params, $domain));
    }

    /**
     * Test createForm.
     */
    public function testCreateForm()
    {
        $formType = 'dummyFormTypeClassName';
        $form     = $this->prophesize(FormInterface::class)->reveal();

        $this->formFactoryProphecy
            ->create($formType, null, [])
            ->shouldBeCalled()
            ->willReturn($form)
        ;

        $this->assertEquals(
            $form,
            $this->helper->createForm($formType)
        );
    }

    /**
     * Test createForm.
     * Complete whole params.
     */
    public function testCreateFormWithWholeParams()
    {
        $formType = 'dummyFormTypeClassName';
        $data     = 'dummyObject';
        $options  = [
            'foo' => 'bar',
        ];

        $form = $this->prophesize(FormInterface::class)->reveal();

        $this->formFactoryProphecy
            ->create($formType, $data, $options)
            ->shouldBeCalled()
            ->willReturn($form)
        ;

        $this->assertEquals(
            $form,
            $this->helper->createForm($formType, $data, $options)
        );
    }
}
