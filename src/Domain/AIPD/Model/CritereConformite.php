<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class CritereConformite
{
    private UuidInterface $id;
    private string $label;
    private bool $isJustificationObligatoire;
    private string $texteConformite;
    private string $texteNonConformiteMineure;
    private string $texteNonConformiteMajeur;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function isJustificationObligatoire(): bool
    {
        return $this->isJustificationObligatoire;
    }

    public function setIsJustificationObligatoire(bool $isJustificationObligatoire): void
    {
        $this->isJustificationObligatoire = $isJustificationObligatoire;
    }

    public function getTexteConformite(): string
    {
        return $this->texteConformite;
    }

    public function setTexteConformite(string $texteConformite): void
    {
        $this->texteConformite = $texteConformite;
    }

    public function getTexteNonConformiteMineure(): string
    {
        return $this->texteNonConformiteMineure;
    }

    public function setTexteNonConformiteMineure(string $texteNonConformiteMineure): void
    {
        $this->texteNonConformiteMineure = $texteNonConformiteMineure;
    }

    public function getTexteNonConformiteMajeur(): string
    {
        return $this->texteNonConformiteMajeur;
    }

    public function setTexteNonConformiteMajeur(string $texteNonConformiteMajeur): void
    {
        $this->texteNonConformiteMajeur = $texteNonConformiteMajeur;
    }
}
