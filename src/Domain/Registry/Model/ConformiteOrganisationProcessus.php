<?php

namespace App\Domain\Registry\Model;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ConformiteOrganisationProcessus
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
     * @var string|null
     */
    private $couleur;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var int|null
     */
    private $position;

    /**
     * @var iterable
     */
    private $questions;

    /**
     * Domain constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id        = Uuid::uuid4();
        $this->questions = [];
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

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(?string $couleur): void
    {
        $this->couleur = $couleur;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    public function addQuestion(ConformiteOrganisationQuestion $question): void
    {
        $this->questions[] = $question;
        $question->setProcessus($this);
    }

    public function removeQuestion(ConformiteOrganisationQuestion $question): void
    {
        $key = \array_search($question, $this->questions, true);

        if (false === $key) {
            return;
        }

        unset($this->questions[$key]);
    }

    public function getQuestions(): iterable
    {
        return $this->questions;
    }
}
