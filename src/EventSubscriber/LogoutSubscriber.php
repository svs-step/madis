<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSubscriber implements EventSubscriberInterface
{
//    private ContainerInterface $container;
    private UrlGeneratorInterface $router;

    public function __construct(
//        ContainerInterface  $container,
        UrlGeneratorInterface $router
    ) {
//        $this->container = $container;
        $this->router = $router;
    }

    public static function getSubscribedEvents(): array
    {
        return [LogoutEvent::class => 'onLogout'];
    }

    public function onLogout(LogoutEvent $event): void
    {
        // Logout from sso service
        // comment recuperer les parametres sso de facon propre ?

//        $sso_type = $this->container->getParameter('SSO_TYPE');
        // TODO only if SSO_TYPE param is set
//        if ($sso_type) {
        $auth_server_url = 'https://cle-integration.sictiam.fr/auth'; // TODO get from config/packages/knpu_oauth2_client.yaml
        $realm           = 'SICTIAM';// TODO get from config/packages/knpu_oauth2_client.yaml
        $redirecturl     = $this->router->generate('login', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $logouturl       = $auth_server_url . '/realms/' . $realm . '/protocol/openid-connect/logout?redirect_uri=' . $redirecturl;

        $response = new RedirectResponse($logouturl);
        $event->setResponse($response);
    }
}
