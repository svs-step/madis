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

namespace App\Application\Controller;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class ControllerHelper
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        Environment $twig,
        RouterInterface $router,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator
    ) {
        $this->twig        = $twig;
        $this->formFactory = $formFactory;
        $this->router      = $router;
        $this->flashBag    = $flashBag;
        $this->translator  = $translator;
    }

    /**
     * Render a Twig template as a response.
     *
     * @param string $name    The name of the template
     * @param array  $context The context of the template
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     *
     * @return Response The response with rendered twig template
     */
    public function render(string $name, array $context = []): Response
    {
        $response = new Response();
        $response->setContent($this->twig->render($name, $context));

        return $response;
    }

    /**
     * Redirect to provided route name
     * - Look for URL of provided route name
     * - Redirect to it.
     *
     * @param string $route      The route name to use for redirect
     * @param array  $parameters The parameters to forward to redirect route
     * @param int    $status     The status of the redirection
     *
     * @return RedirectResponse The redirect response
     */
    public function redirectToRoute(string $route, array $parameters = [], int $status = 302): RedirectResponse
    {
        return new RedirectResponse(
            $this->router->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_PATH),
            $status
        );
    }

    /**
     * Add Flashbag in session.
     *
     * @param string $type    The flashbag type
     * @param string $message The message of the flashbag
     */
    public function addFlash(string $type, string $message): void
    {
        $this->flashBag->add($type, $message);
    }

    /**
     * Translate a key.
     *
     * @param string $key        The key to translate
     * @param array  $parameters Parameters to use during translation
     * @param string $domain     The domain on which look for translation
     *
     * @return string The translated key
     */
    public function trans(string $key, array $parameters = [], ?string $domain = null): string
    {
        return $this->translator->trans($key, $parameters, $domain);
    }

    /**
     * Create a FormType.
     *
     * @param string      $type    The FormType name
     * @param object|null $data    The data to use to create FormType
     * @param array       $options The FormType options
     *
     * @return FormInterface The generated FormType
     */
    public function createForm(string $type, $data = null, array $options = [])
    {
        return $this->formFactory->create($type, $data, $options);
    }
}
