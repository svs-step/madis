<?php

namespace App\Domain\Notification\Event;

use App\Domain\Registry\Model\Request;
use Symfony\Contracts\EventDispatcher\Event;

class LateRequestEvent extends Event
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }
}
