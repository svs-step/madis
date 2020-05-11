<?php

namespace App\Domain\Registry\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ConformiteOrganisationQuestion
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var string|null
     */
    private $nom;

    /**
     * @var ConformiteOrganisationProcessus|null
     */
    private $processus;

    /**
     * @var iterable
     */
    private $reponses;

    /**
     * Question constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id       = Uuid::uuid4();
        $this->reponses = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }

    public function getProcessus(): ?ConformiteOrganisationProcessus
    {
        return $this->processus;
    }

    public function setProcessus(?ConformiteOrganisationProcessus $processus): void
    {
        $this->processus = $processus;
    }

    public function getReponses(): iterable
    {
        return $this->reponses;
    }
}
