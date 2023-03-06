<?php

namespace App\Domain\Registry\Model;

use App\Domain\Reporting\Model\LoggableSubject;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ShelfLife implements LoggableSubject
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $duration;

    /**
     * @var string
     */
    private $ultimateFate;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(string $duration): void
    {
        $this->duration = $duration;
    }

    public function getUltimateFate(): ?string
    {
        return $this->ultimateFate;
    }

    public function setUltimateFate(string $ultimateFate): void
    {
        $this->ultimateFate = $ultimateFate;
    }
}
