<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

use App\Application\Traits\Model\HistoryTrait;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ModeleAnalyse
{
    use HistoryTrait;

    private UuidInterface $id;

    private string $nom;
    private string $description;

    private string $labelAmeliorationPrevue;

    private string $labelInsatisfaisant;

    private string $labelSatisfaisant;

    /**
     * @var array|CriterePrincipeFondamental[]
     */
    private $criterePrincipeFondamentaux;

    /**
     * @var array|ModeleAnalyseQuestionConformite[]
     */
    private $questionConformites;

    /**
     * @var array|ModeleScenarioMenace[]
     */
    private $scenarioMenaces;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getLabelAmeliorationPrevue(): string
    {
        return $this->labelAmeliorationPrevue;
    }

    public function setLabelAmeliorationPrevue(string $labelAmeliorationPrevue): void
    {
        $this->labelAmeliorationPrevue = $labelAmeliorationPrevue;
    }

    public function getLabelInsatisfaisant(): string
    {
        return $this->labelInsatisfaisant;
    }

    public function setLabelInsatisfaisant(string $labelInsatisfaisant): void
    {
        $this->labelInsatisfaisant = $labelInsatisfaisant;
    }

    public function getLabelSatisfaisant(): string
    {
        return $this->labelSatisfaisant;
    }

    public function setLabelSatisfaisant(string $labelSatisfaisant): void
    {
        $this->labelSatisfaisant = $labelSatisfaisant;
    }

    public function getCriterePrincipeFondamentaux()
    {
        return $this->criterePrincipeFondamentaux;
    }

    public function setCriterePrincipeFondamentaux($criterePrincipeFondamentaux): void
    {
        /** @var CriterePrincipeFondamental $critere */
        foreach ($criterePrincipeFondamentaux as $critere) {
            $critere->setModeleAnalyse($this);
        }
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

    public function getScenarioMenaces()
    {
        return $this->scenarioMenaces;
    }

    public function setScenarioMenaces($scenarioMenaces): void
    {
        foreach ($scenarioMenaces as $scenarioMenace) {
            $scenarioMenace->setModeleAnalyse($this);
        }
        $this->scenarioMenaces = $scenarioMenaces;
    }
}
