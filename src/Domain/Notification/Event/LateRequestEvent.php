<?php

namespace App\Domain\Notification\Event;

use App\Domain\Registry\Model\Mesurement;
use App\Domain\Registry\Model\Request;
use App\Domain\Registry\Model\Violation;
use Symfony\Contracts\EventDispatcher\Event;

class LateRequestEvent extends Event
{
    /**
     * @var Request
     * The object that generated the notification
     */
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }
}