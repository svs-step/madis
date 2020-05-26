<?php

namespace App\Domain\Registry\Model\ConformiteOrganisation;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Reponse
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var int|null
     */
    private $reponse;

    /**
     * @var string|null
     */
    private $reponseRaison;

    /**
     * @var Question
     */
    private $question;

    /**
     * @var Conformite
     */
    private $conformite;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getReponse(): ?int
    {
        return $this->reponse;
    }

    public function setReponse(?int $reponse): void
    {
        $this->reponse = $reponse;
    }

    public function getReponseRaison(): ?string
    {
        return $this->reponseRaison;
    }

    public function setReponseRaison(?string $reponseRaison): void
    {
        $this->reponseRaison = $reponseRaison;
    }

    public function getQuestion(): Question
    {
        return $this->question;
    }

    public function setQuestion(Question $question): void
    {
        $this->question = $question;
    }

    public function getConformite(): Conformite
    {
        return $this->conformite;
    }

    public function setConformite(Conformite $conformite): void
    {
        $this->conformite = $conformite;
    }
}
