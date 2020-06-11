<?php

namespace App\Domain\Registry\Model\ConformiteOrganisation;

use App\Domain\Registry\Dictionary\MesurementStatusDictionary;
use App\Domain\Registry\Model\Mesurement;
use Ramsey\Uuid\Uuid;

/**
 * Modelize the relation between Processus and Evaluation.
 * Contain the conformité rating.
 */
class Conformite
{
    /**
     * @var Uuid
     */
    private $id;

    /**
     * @var float|null
     */
    private $conformite;

    /**
     * @var Processus
     */
    private $processus;

    /**
     * @var Evaluation
     */
    private $evaluation;

    /**
     * @var iterable
     */
    private $actionProtections;

    /**
     * @var iterable
     */
    private $reponses;

    /**
     * @var string
     */
    private $pilote;

    public function __construct()
    {
        $this->id                = Uuid::uuid4();
        $this->reponses          = [];
        $this->actionProtections = [];
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getConformite(): ?float
    {
        return $this->conformite;
    }

    public function setConformite(?float $conformite): void
    {
        $this->conformite = $conformite;
    }

    public function getProcessus(): Processus
    {
        return $this->processus;
    }

    public function setProcessus(Processus $processus): void
    {
        $this->processus = $processus;
    }

    public function getEvaluation(): Evaluation
    {
        return $this->evaluation;
    }

    public function setEvaluation(Evaluation $evaluation): void
    {
        $this->evaluation = $evaluation;
    }

    public function getActionProtections(): iterable
    {
        return $this->actionProtections;
    }

    public function getNonAppliedActionProtections()
    {
        return array_filter(\iterable_to_array($this->actionProtections),
            function (Mesurement $action) {
                return MesurementStatusDictionary::STATUS_NOT_APPLIED === $action->getStatus();
            });
    }

    public function setActionProtections(iterable $actionProtections): void
    {
        $this->actionProtections = $actionProtections;
    }

    public function addReponse(Reponse $reponse): void
    {
        $this->reponses[] = $reponse;
        $reponse->setConformite($this);
    }

    public function removeReponse(Reponse $reponse): void
    {
        $key = \array_search($reponse, $this->reponses, true);

        if (false === $key) {
            return;
        }

        unset($this->reponses[$key]);
    }

    public function getReponses(): iterable
    {
        return $this->reponses;
    }

    public function getPilote(): string
    {
        return $this->pilote;
    }

    public function setPilote(string $pilote): void
    {
        $this->pilote = $pilote;
    }
}
