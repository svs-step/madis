<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSubscriber implements EventSubscriberInterface
{
    private UrlGeneratorInterface $router;
    private ?string $logoutUrl;

    public function __construct(
        UrlGeneratorInterface $router,
        ?string $logoutUrl,
    ) {
        $this->router    = $router;
        $this->logoutUrl = $logoutUrl;
    }

    public static function getSubscribedEvents(): array
    {
        return [LogoutEvent::class => 'onLogout'];
    }

    public function onLogout(LogoutEvent $event): void
    {
        // Logout from sso service
        if ($this->logoutUrl) {
            $redirectUrl = $this->router->generate('login', [], UrlGeneratorInterface::ABSOLUTE_URL);
            $url         = $this->logoutUrl . '?redirect_uri=' . $redirectUrl;
            $response    = new RedirectResponse($url);
            $event->setResponse($response);
        }
    }
}
