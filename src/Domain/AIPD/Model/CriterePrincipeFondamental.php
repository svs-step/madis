<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

use App\Domain\AIPD\Dictionary\ReponseCritereFondamental;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class CriterePrincipeFondamental
{
    private UuidInterface $id;

    private ModeleAnalyse $modeleAnalyse;
    private string $label;
    private string $labelLivrable;
    /**
     * @see ReponseCritereFondamental
     */
    private string $reponse;
    private bool $isVisible;
    private string $texteConformite;
    private string $texteNonConformite;
    private string $texteNonApplicable;
    private string $justification;
    private $fichier; //TODO File

    public function __construct(string $label = null)
    {
        $this->id = Uuid::uuid4();
        if (!\is_null($label)) {
            $this->label = $label;
        }
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getModeleAnalyse(): ModeleAnalyse
    {
        return $this->modeleAnalyse;
    }

    public function setModeleAnalyse(ModeleAnalyse $modeleAnalyse): void
    {
        $this->modeleAnalyse = $modeleAnalyse;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getLabelLivrable(): string
    {
        return $this->labelLivrable;
    }

    public function setLabelLivrable(string $labelLivrable): void
    {
        $this->labelLivrable = $labelLivrable;
    }

    public function getReponse()
    {
        return $this->reponse;
    }

    public function setReponse($reponse): void
    {
        $this->reponse = $reponse;
    }

    public function isVisible(): bool
    {
        return $this->isVisible;
    }

    public function setIsVisible(bool $isVisible): void
    {
        $this->isVisible = $isVisible;
    }

    public function getTexteConformite(): string
    {
        return $this->texteConformite;
    }

    public function setTexteConformite(string $texteConformite): void
    {
        $this->texteConformite = $texteConformite;
    }

    public function getTexteNonConformite(): string
    {
        return $this->texteNonConformite;
    }

    public function setTexteNonConformite(string $texteNonConformite): void
    {
        $this->texteNonConformite = $texteNonConformite;
    }

    public function getTexteNonApplicable(): string
    {
        return $this->texteNonApplicable;
    }

    public function setTexteNonApplicable(string $texteNonApplicable): void
    {
        $this->texteNonApplicable = $texteNonApplicable;
    }

    public function getJustification(): string
    {
        return $this->justification;
    }

    public function setJustification(string $justification): void
    {
        $this->justification = $justification;
    }

    public function getFichier()
    {
        return $this->fichier;
    }

    public function setFichier($fichier): void
    {
        $this->fichier = $fichier;
    }
}
