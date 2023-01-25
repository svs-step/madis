<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

use App\Domain\AIPD\Dictionary\VraisemblanceGraviteDictionary;
use Doctrine\ORM\PersistentCollection;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @Serializer\ExclusionPolicy("none")
 */
abstract class AbstractScenarioMenace
{
    /**
     * @Serializer\Exclude
     */
    protected UuidInterface $id;

    protected string $nom;

    /**
     * @var array|PersistentCollection|AnalyseMesureProtection[]|ModeleMesureProtection[]
     *
     * @Serializer\Type("array<App\Domain\AIPD\Model\ModeleMesureProtection>")
     */
    protected $mesuresProtections;

    protected bool $isVisible;
    protected bool $isDisponibilite;
    protected bool $isIntegrite;
    protected bool $isConfidentialite;
    /**
     * @see VraisemblanceGraviteDictionary
     */
    protected string $vraisemblance;
    /**
     * @see VraisemblanceGraviteDictionary
     */
    protected string $gravite;
    protected ?string $precisions;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function __clone()
    {
        $this->id                 = Uuid::uuid4();
        $this->mesuresProtections = (clone $this->mesuresProtections)->toArray();
    }

    public function deserialize(): void
    {
        $this->id = Uuid::uuid4();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($nom): void
    {
        $this->nom = $nom;
    }

    public function getMesuresProtections()
    {
        return $this->mesuresProtections;
    }

    public function setMesuresProtections($mesureProtections): void
    {
        $this->mesuresProtections = $mesureProtections;
    }

    public function isVisible(): bool
    {
        return $this->isVisible;
    }

    public function setIsVisible(bool $isVisible): void
    {
        $this->isVisible = $isVisible;
    }

    public function isDisponibilite(): bool
    {
        return $this->isDisponibilite;
    }

    public function setIsDisponibilite(bool $isDisponibilite): void
    {
        $this->isDisponibilite = $isDisponibilite;
    }

    public function isIntegrite(): bool
    {
        return $this->isIntegrite;
    }

    public function setIsIntegrite(bool $isIntegrite): void
    {
        $this->isIntegrite = $isIntegrite;
    }

    public function isConfidentialite(): bool
    {
        return $this->isConfidentialite;
    }

    public function setIsConfidentialite(bool $isConfidentialite): void
    {
        $this->isConfidentialite = $isConfidentialite;
    }

    public function getVraisemblance()
    {
        return $this->vraisemblance;
    }

    public function setVraisemblance($vraisemblance): void
    {
        $this->vraisemblance = $vraisemblance;
    }

    public function getGravite()
    {
        return $this->gravite;
    }

    public function setGravite($gravite): void
    {
        $this->gravite = $gravite;
    }

    public function getPrecisions(): ?string
    {
        return $this->precisions;
    }

    public function setPrecisions(?string $precisions): void
    {
        $this->precisions = $precisions;
    }
}
