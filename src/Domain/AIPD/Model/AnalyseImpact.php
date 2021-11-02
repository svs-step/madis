<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

use App\Application\Traits\Model\HistoryTrait;
use App\Domain\AIPD\Dictionary\StatutAnalyseImpactDictionary;
use App\Domain\Registry\Model\ConformiteTraitement\ConformiteTraitement;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class AnalyseImpact
{
    use HistoryTrait;

    private UuidInterface $id;

    /**
     * @see StatutAnalyseImpactDictionary
     */
    private string $statut;

    private ConformiteTraitement $conformiteTraitement;

    /**
     * Since any changes on the ModeleAnalyse should not be repercuted on the AnalyseImpact,
     * we store only the name of the ModeleAnalyse for information purposes.
     */
    private string $modeleAnalyse;

    /**
     * @var array|CriterePrincipeFondamental[]
     */
    private $criterePrincipeFondamentaux;

    /**
     * @var array|AnalyseQuestionConformite[]
     */
    private $questionConformites;

    /**
     * @var array|AnalyseScenarioMenace[]
     */
    private $scenarioMenaces;

    private ?\DateTime $dateValidation;

    /**
     * @var array|AnalyseMesureProtection[]
     */
    private $mesureProtections;

    private bool $isReadyForValidation = false;

    private AnalyseAvis $avisReferent;
    private AnalyseAvis $avisDpd;
    private AnalyseAvis $avisRepresentant;
    private AnalyseAvis $avisResponsable;

    private $isValidated = false;

    public function __construct()
    {
        $this->id               = Uuid::uuid4();
        $this->statut           = StatutAnalyseImpactDictionary::EN_COURS;
        $this->avisReferent     = new AnalyseAvis();
        $this->avisDpd          = new AnalyseAvis();
        $this->avisRepresentant = new AnalyseAvis();
        $this->avisResponsable  = new AnalyseAvis();
    }

    public function __toString()
    {
        return $this->conformiteTraitement->getTraitement()->getName() . ' du ' . date_format($this->createdAt, 'd/m/Y');
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): void
    {
        $this->statut = $statut;
    }

    public function getConformiteTraitement(): ConformiteTraitement
    {
        return $this->conformiteTraitement;
    }

    public function setConformiteTraitement(ConformiteTraitement $conformiteTraitement): void
    {
        $this->conformiteTraitement = $conformiteTraitement;
    }

    public function getModeleAnalyse(): string
    {
        return $this->modeleAnalyse;
    }

    public function setModeleAnalyse(string $modeleAnalyse): void
    {
        $this->modeleAnalyse = $modeleAnalyse;
    }

    public function getCriterePrincipeFondamentaux()
    {
        return $this->criterePrincipeFondamentaux;
    }

    public function setCriterePrincipeFondamentaux($criterePrincipeFondamentaux): void
    {
        $this->criterePrincipeFondamentaux = $criterePrincipeFondamentaux;
    }

    public function getQuestionConformites()
    {
        return $this->questionConformites;
    }

    public function setQuestionConformites($questionConformites): void
    {
        $this->questionConformites = $questionConformites;
    }

    public function getDateValidation(): ?\DateTime
    {
        return $this->dateValidation;
    }

    public function setDateValidation(?\DateTime $dateValidation): void
    {
        $this->dateValidation = $dateValidation;
    }

    public function getScenarioMenaces()
    {
        return $this->scenarioMenaces;
    }

    public function setScenarioMenaces($scenarioMenaces): void
    {
        $this->scenarioMenaces = $scenarioMenaces;
    }

    public function getMesureProtections()
    {
        $mesures = [];
        foreach ($this->scenarioMenaces as $scenario) {
            foreach ($scenario->getMesuresProtections() as $mesureProtection) {
                $mesures[] = $mesureProtection;
            }
        }

        return $mesures;
    }

    public function isReadyForValidation(): bool
    {
        return $this->isReadyForValidation;
    }

    public function setIsReadyForValidation(bool $isReadyForValidation): void
    {
        $this->isReadyForValidation = $isReadyForValidation;
    }

    public function getAvisReferent(): AnalyseAvis
    {
        return $this->avisReferent;
    }

    public function setAvisReferent(AnalyseAvis $avisReferent): void
    {
        $this->avisReferent = $avisReferent;
    }

    public function getAvisDpd(): AnalyseAvis
    {
        return $this->avisDpd;
    }

    public function setAvisDpd(AnalyseAvis $avisDpd): void
    {
        $this->avisDpd = $avisDpd;
    }

    public function getAvisRepresentant(): AnalyseAvis
    {
        return $this->avisRepresentant;
    }

    public function setAvisRepresentant(AnalyseAvis $avisRepresentant): void
    {
        $this->avisRepresentant = $avisRepresentant;
    }

    public function getAvisResponsable(): AnalyseAvis
    {
        return $this->avisResponsable;
    }

    public function setAvisResponsable(AnalyseAvis $avisResponsable): void
    {
        $this->avisResponsable = $avisResponsable;
    }

    public function isValidated(): bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(bool $isValidated): void
    {
        $this->isValidated = $isValidated;
    }
}
