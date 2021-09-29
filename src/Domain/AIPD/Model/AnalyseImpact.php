<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

use App\Domain\AIPD\Dictionary\StatutAnalyseImpactDictionary;
use App\Domain\Registry\Model\ConformiteTraitement\ConformiteTraitement;
use Gedmo\Timestampable\Traits\Timestampable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class AnalyseImpact
{
    use Timestampable;

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

    public function __construct()
    {
        $this->id     = Uuid::uuid4();
        $this->statut = StatutAnalyseImpactDictionary::EN_COURS;
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

    public function getDateValidation(): \DateTime
    {
        return $this->dateValidation;
    }

    public function setDateValidation(\DateTime $dateValidation): void
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
}
