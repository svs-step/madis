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

namespace App\Application\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class ControllerHelper
{
    /**
     * @var Environment
     */
    private $twig;

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
        FlashBagInterface $flashBag,
        TranslatorInterface $translator
    ) {
        $this->twig       = $twig;
        $this->router     = $router;
        $this->flashBag   = $flashBag;
        $this->translator = $translator;
    }

    /**
     * Render a Twig template as a response.
     *
     * @param string $name    The name of the template
     * @param array  $context The context of the template
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
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
}
