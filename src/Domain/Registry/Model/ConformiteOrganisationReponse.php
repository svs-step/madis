<?php

namespace App\Domain\Registry\Model;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ConformiteOrganisationReponse
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
     * @var ConformiteOrganisationQuestion
     */
    private $question;

    /**
     * @var ConformiteOrganisationEvaluation
     */
    private $evaluation;

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

    public function getQuestion(): ConformiteOrganisationQuestion
    {
        return $this->question;
    }

    public function setQuestion(ConformiteOrganisationQuestion $question): void
    {
        $this->question = $question;
    }

    public function getEvaluation(): ConformiteOrganisationEvaluation
    {
        return $this->evaluation;
    }

    public function setEvaluation(ConformiteOrganisationEvaluation $evaluation): void
    {
        $this->evaluation = $evaluation;
    }
}
