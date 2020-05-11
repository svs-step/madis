<?php

namespace App\Domain\Registry\Model;

use Ramsey\Uuid\Uuid;

/**
 * Modelize the relation between Processus and Evaluation.
 * Contain the conformité rating.
 */
class ConformiteOrganisationConformite
{
    /**
     * @var Uuid
     */
    private $id;

    /**
     * @var int|null
     */
    private $conformite;

    /**
     * @var ConformiteOrganisationProcessus
     */
    private $processus;

    /**
     * @var ConformiteOrganisationEvaluation
     */
    private $evaluation;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getConformite(): ?int
    {
        return $this->conformite;
    }

    public function setConformite(?int $conformite): void
    {
        $this->conformite = $conformite;
    }

    public function getProcessus(): ConformiteOrganisationProcessus
    {
        return $this->processus;
    }

    public function setProcessus(ConformiteOrganisationProcessus $processus): void
    {
        $this->processus = $processus;
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
