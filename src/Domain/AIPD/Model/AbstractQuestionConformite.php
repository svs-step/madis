<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class AbstractQuestionConformite
{
    protected UuidInterface $id;
    protected string $question;
    protected int $position;
    protected bool $isJustificationObligatoire;
    protected ?string $texteConformite;
    protected ?string $texteNonConformiteMineure;
    protected ?string $texteNonConformiteMajeure;

    public function __construct(string $question, int $position)
    {
        $this->id       = Uuid::uuid4();
        $this->question = $question;
        $this->position = $position;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getQuestion(): string
    {
        return $this->question;
    }

    public function setQuestion(string $question): void
    {
        $this->question = $question;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function isJustificationObligatoire(): bool
    {
        return $this->isJustificationObligatoire;
    }

    public function setIsJustificationObligatoire(bool $isJustificationObligatoire): void
    {
        $this->isJustificationObligatoire = $isJustificationObligatoire;
    }

    public function getTexteConformite(): ?string
    {
        return $this->texteConformite;
    }

    public function setTexteConformite(?string $texteConformite): void
    {
        $this->texteConformite = $texteConformite;
    }

    public function getTexteNonConformiteMineure(): ?string
    {
        return $this->texteNonConformiteMineure;
    }

    public function setTexteNonConformiteMineure(?string $texteNonConformiteMineure): void
    {
        $this->texteNonConformiteMineure = $texteNonConformiteMineure;
    }

    public function getTexteNonConformiteMajeure(): ?string
    {
        return $this->texteNonConformiteMajeure;
    }

    public function setTexteNonConformiteMajeure(?string $texteNonConformiteMajeure): void
    {
        $this->texteNonConformiteMajeure = $texteNonConformiteMajeure;
    }
}
