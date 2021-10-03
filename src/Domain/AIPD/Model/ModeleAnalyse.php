<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

use App\Application\Traits\Model\HistoryTrait;
use App\Domain\User\Model\Collectivity;
use Doctrine\Common\Collections\Collection;
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
     * @var Collection|Collectivity[]
     */
    private $authorizedCollectivities;

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

    /**
     * @see DuplicationTargetOptionDictionary
     */
    private ?string $optionRightSelection;

    /**
     * @see CollectivityTypeDictionary
     */
    private ?iterable $authorizedCollectivityTypes;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function __toString(): string
    {
        if (\is_null($this->getNom())) {
            return '';
        }

        if (\mb_strlen($this->getNom()) > 50) {
            return \mb_substr($this->getNom(), 0, 50) . '...';
        }

        return $this->getNom();
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

    public function getAuthorizedCollectivities()
    {
        return $this->authorizedCollectivities;
    }

    public function setAuthorizedCollectivities($authorizedCollectivities): void
    {
        $this->authorizedCollectivities = $authorizedCollectivities;
    }

    public function addAuthorizedCollectivity(Collectivity $collectivity)
    {
        if ($this->authorizedCollectivities->contains($collectivity)) {
            return;
        }

        $this->authorizedCollectivities[] = $collectivity;
        $collectivity->addModeleAnalyse($this);
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

    public function getOptionRightSelection()
    {
        return $this->optionRightSelection;
    }

    public function setOptionRightSelection(iterable $optionRightSelection)
    {
        $this->optionRightSelection = $optionRightSelection;
    }

    public function getAuthorizedCollectivityTypes()
    {
        return $this->authorizedCollectivityTypes;
    }

    public function setAuthorizedCollectivityTypes(iterable $authorizedCollectivityTypes)
    {
        $this->authorizedCollectivityTypes = $authorizedCollectivityTypes;
    }
}
