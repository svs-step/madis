<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSubscriber implements EventSubscriberInterface
{
    public function __construct(){
    }

    public static function getSubscribedEvents(): array
    {
        return [LogoutEvent::class => 'onLogout'];
    }

    public function onLogout(LogoutEvent $event): void
    {
        $session = $event->getRequest()->getSession();
        if ($session->has('ssoLogoutUrl')) {
            $response = new RedirectResponse($session->get('ssoLogoutUrl'));
            $event->setResponse($response);
        }
        $session->clear();
    }
}
