<?php

declare(strict_types=1);

namespace App\Application\Symfony\EventSubscriber\Kernel;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class IdleSubscriber
 * Check user idle time.
 * If user is idle more than accepted time, invalidate his session.
 */
class IdleSubscriber implements EventSubscriberInterface
{
    /**
     * @var int
     */
    private $expirationTime;

    /**
     * IdleSubscriber constructor.
     *
     * @param int $expirationTime Time in seconds to define idle
     */
    public function __construct(int $expirationTime)
    {
        $this->expirationTime = $expirationTime;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['onKernelRequest', 9],
            ],
        ];
    }

    /**
     * OnKernelRequest check idle since last Request.
     * If idle is over, then invalidate session.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $session = $request->getSession();

        $session->start();
        $metaData       = $session->getMetadataBag();
        $timeDifference = time() - $metaData->getLastUsed();

        if ($timeDifference > $this->expirationTime) {
            $session->invalidate();
        }
    }
}
