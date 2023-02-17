<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class AbstractMesureProtection
{
    /**
     * @Serializer\Accessor(getter="getIdString",setter="setIdFromString")
     * @Serializer\Type("string")
     */
    private UuidInterface $id;
    private string $nom;
    private string $nomCourt;
    private string $labelLivrable;
    private string $phrasePreconisation;
    private string $detail;
    private int $poidsVraisemblance;
    private int $poidsGravite;

    /**
     * @var \DateTimeImmutable|null
     *
     * @Serializer\Type("DateTimeImmutable")
     */
    private $createdAt;

    /**
     * @var \DateTimeImmutable|null
     *
     * @Serializer\Type("DateTimeImmutable")
     */
    private $updatedAt;


    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIdString()
    {
        return $this->id->toString();
    }

    public function setIdFromString(string $str)
    {
        $this->id = Uuid::fromString($str);
    }

    public function __toString(): string
    {
        return $this->nom;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function getNomCourt(): string
    {
        return $this->nomCourt;
    }

    public function setNomCourt(string $nomCourt): void
    {
        $this->nomCourt = $nomCourt;
    }

    public function getLabelLivrable(): string
    {
        return $this->labelLivrable;
    }

    public function setLabelLivrable(string $labelLivrable): void
    {
        $this->labelLivrable = $labelLivrable;
    }

    public function getPhrasePreconisation(): string
    {
        return $this->phrasePreconisation;
    }

    public function setPhrasePreconisation(string $phrasePreconisation): void
    {
        $this->phrasePreconisation = $phrasePreconisation;
    }

    public function getDetail(): string
    {
        return $this->detail;
    }

    public function setDetail(string $detail): void
    {
        $this->detail = $detail;
    }

    public function getPoidsVraisemblance(): int
    {
        return $this->poidsVraisemblance;
    }

    public function setPoidsVraisemblance(int $poidsVraisemblance): void
    {
        $this->poidsVraisemblance = $poidsVraisemblance;
    }

    public function getPoidsGravite(): int
    {
        return $this->poidsGravite;
    }

    public function setPoidsGravite(int $poidsGravite): void
    {
        $this->poidsGravite = $poidsGravite;
    }
}
