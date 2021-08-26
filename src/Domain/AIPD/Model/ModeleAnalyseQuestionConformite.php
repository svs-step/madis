<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ModeleAnalyseQuestionConformite
{
    private UuidInterface $id;
    private string $question;
    private bool $isJustificationObligatoire;
    private ?string $texteConformite;
    private ?string $texteNonConformiteMineure;
    private ?string $texteNonConformiteMajeure;
    private ModeleAnalyse $modeleAnalyse;

    public function __construct(string $question, ?ModeleAnalyse $modeleAnalyse = null)
    {
        $this->id       = Uuid::uuid4();
        $this->question = $question;
        if (!is_null($modeleAnalyse)) {
            $this->modeleAnalyse = $modeleAnalyse;
        }
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

    public function getIsJustificationObligatoire(): bool
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
