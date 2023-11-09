<?php

namespace App\Domain\Registry\Model;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ShelfLife
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $duration;

    /**
     * @var string|null
     */
    public $ultimateFate;

    /**
     * @var Treatment
     */
    private $treatment;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getTreatment(): Treatment
    {
        return $this->treatment;
    }

    public function setTreatment(Treatment $treatment): void
    {
        $this->treatment = $treatment;
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

    public function setUltimateFate(?string $ultimateFate): void
    {
        $this->ultimateFate = $ultimateFate;
    }
}
