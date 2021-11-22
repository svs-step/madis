<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @Serializer\ExclusionPolicy("none")
 */
class CriterePrincipeFondamental
{
    /**
     * @Serializer\Exclude
     */
    protected UuidInterface $id;

    protected string $label;
    protected string $labelLivrable;
    /**
     * @see ReponseCritereFondamentalDictionary
     */
    private string $reponse;
    private bool $isVisible;
    private string $texteConformite;
    private string $texteNonConformite;
    private string $texteNonApplicable;
    private ?string $justification;
    private ?string $fichier;
    private ?UploadedFile $fichierFile = null;

    /**
     * @Serializer\Exclude
     */
    private ?ModeleAnalyse $modeleAnalyse;

    /**
     * @Serializer\Exclude
     */
    private ?AnalyseImpact $analyseImpact;

    public function __construct(string $label = null)
    {
        $this->id = Uuid::uuid4();
        if (!\is_null($label)) {
            $this->label = $label;
        }
    }

    public function __clone()
    {
        $this->id = Uuid::uuid4();
    }

    public function deserialize(): void
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

    public function getJustification(): ?string
    {
        return $this->justification;
    }

    public function setJustification(?string $justification): void
    {
        $this->justification = $justification;
    }

    public function getFichier(): ?string
    {
        return $this->fichier;
    }

    public function setFichier(?string $fichier): void
    {
        $this->fichier = $fichier;
    }

    public function getFichierFile(): ?UploadedFile
    {
        return $this->fichierFile;
    }

    public function setFichierFile(?UploadedFile $fichierFile): void
    {
        $this->fichierFile = $fichierFile;
    }

    public function getModeleAnalyse(): ?ModeleAnalyse
    {
        return $this->modeleAnalyse;
    }

    public function setModeleAnalyse(?ModeleAnalyse $modeleAnalyse): void
    {
        $this->modeleAnalyse = $modeleAnalyse;
    }

    public function getAnalyseImpact(): ?AnalyseImpact
    {
        return $this->analyseImpact;
    }

    public function setAnalyseImpact(?AnalyseImpact $analyseImpact): void
    {
        $this->analyseImpact = $analyseImpact;
    }
}
