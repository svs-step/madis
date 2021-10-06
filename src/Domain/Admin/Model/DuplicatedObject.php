<?php

declare(strict_types=1);

namespace App\Domain\Admin\Model;

use App\Domain\User\Model\Collectivity;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class DuplicatedObject
{
    private UuidInterface $id;
    private Duplication $duplication;

    private Collectivity $collectivity;

    private ?string $duplicatId;

    private string $originObjectId;

    public function __construct(Duplication $duplication, Collectivity $collectivity, $originObjectId)
    {
        $this->id             = Uuid::uuid4();
        $this->duplication    = $duplication;
        $this->collectivity   = $collectivity;
        $this->originObjectId = $originObjectId;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function setId(UuidInterface $id): void
    {
        $this->id = $id;
    }

    public function getDuplication(): Duplication
    {
        return $this->duplication;
    }

    public function setDuplication(Duplication $duplication): void
    {
        $this->duplication = $duplication;
    }

    public function getCollectivity(): Collectivity
    {
        return $this->collectivity;
    }

    public function setCollectivity(Collectivity $collectivity): void
    {
        $this->collectivity = $collectivity;
    }

    public function getDuplicatId(): ?string
    {
        return $this->duplicatId;
    }

    public function setDuplicatId(?string $duplicatId): void
    {
        $this->duplicatId = $duplicatId;
    }

    public function getOriginObjectId(): string
    {
        return $this->originObjectId;
    }

    public function setOriginObjectId(string $originObjectId): void
    {
        $this->originObjectId = $originObjectId;
    }
}
