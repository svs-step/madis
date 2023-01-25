<?php

namespace App\Domain\Notification\Event;

use App\Domain\Registry\Model\ConformiteTraitement\ConformiteTraitement;
use Symfony\Contracts\EventDispatcher\Event;

class ConformiteTraitementNeedsAIPDEvent extends Event
{
    /**
     * The object that generated the notification.
     */
    protected ConformiteTraitement $conformiteTraitement;

    public function __construct(ConformiteTraitement $conformiteTraitement)
    {
        $this->conformiteTraitement = $conformiteTraitement;
    }

    public function getConformiteTraitement(): ConformiteTraitement
    {
        return $this->conformiteTraitement;
    }

    public function setConformiteTraitement(ConformiteTraitement $conformiteTraitement): void
    {
        $this->conformiteTraitement = $conformiteTraitement;
    }
}
