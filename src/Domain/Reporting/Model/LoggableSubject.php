<?php

namespace App\Domain\Reporting\Model;

use Ramsey\Uuid\Uuid;

/**
 * Permet l'enregistrement de l'object dans LogJournal.
 */
abstract class LoggableSubject
{
    /**
     * @var Uuid
     */
    protected $id;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }
}
