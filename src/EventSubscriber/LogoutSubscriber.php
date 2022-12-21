<?php

namespace App\EventSubscriber;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSubscriber implements EventSubscriberInterface
{
    private UrlGeneratorInterface $router;
    private ContainerInterface $container;

    public function __construct(
        ContainerInterface $container,
        UrlGeneratorInterface $router
    ) {
        $this->container = $container;
        $this->router    = $router;
    }

    public static function getSubscribedEvents(): array
    {
        return [LogoutEvent::class => 'onLogout'];
    }

    public function onLogout(LogoutEvent $event): void
    {
        // Logout from sso service
        $logoutUrl = $this->container->getParameter('SSO_LOGOUT_URL');

        if ($logoutUrl) {
            $redirectUrl = $this->router->generate('login', [], UrlGeneratorInterface::ABSOLUTE_URL);
            $logoutUrl .= '?redirect_uri=' . $redirectUrl;
            $response = new RedirectResponse($logoutUrl);
            $event->setResponse($response);
        }
    }
}
