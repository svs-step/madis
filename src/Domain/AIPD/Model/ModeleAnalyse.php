<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

use App\Application\Traits\Model\HistoryTrait;
use App\Domain\User\Model\Collectivity;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @Serializer\ExclusionPolicy("none")
 */
class ModeleAnalyse
{
    use HistoryTrait;

    /**
     * @Serializer\Exclude
     */
    private ?UuidInterface $id;

    private string $nom;
    private string $description;

    private string $labelAmeliorationPrevue;

    private string $labelInsatisfaisant;

    private string $labelSatisfaisant;

    /**
     * @var Collection|Collectivity[]
     * @Serializer\Exclude
     */
    private $authorizedCollectivities;

    /**
     * @var array|CriterePrincipeFondamental[]
     * @Serializer\Type("array<App\Domain\AIPD\Model\CriterePrincipeFondamental>")
     */
    private iterable $criterePrincipeFondamentaux;

    /**
     * @var array|ModeleQuestionConformite[]
     * @Serializer\Type("array<App\Domain\AIPD\Model\ModeleQuestionConformite>")
     */
    private $questionConformites;

    /**
     * @var array|ModeleScenarioMenace[]
     * @Serializer\Type("array<App\Domain\AIPD\Model\ModeleScenarioMenace>")
     */
    private iterable $scenarioMenaces;

    /**
     * @see DuplicationTargetOptionDictionary
     * @Serializer\Exclude
     */
    private ?string $optionRightSelection = null;

    /**
     * @see CollectivityTypeDictionary
     * @Serializer\Exclude
     */
    private ?iterable $authorizedCollectivityTypes;

    /**
     * @var \DateTimeImmutable|null
     * @Serializer\Type("DateTimeImmutable")
     */
    private $createdAt;

    /**
     * @var \DateTimeImmutable|null
     * @Serializer\Type("DateTimeImmutable")
     */
    private $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function __clone()
    {
        $this->id                       = null;
        $this->authorizedCollectivities = null;

        $questions = [];
        foreach ($this->questionConformites as $questionConformite) {
            $questions[] = clone $questionConformite;
        }
        $this->questionConformites = $questions;

        $scenarios = [];
        foreach ($this->scenarioMenaces as $scenario) {
            $scenarios[] = clone $scenario;
        }
        $this->scenarioMenaces = $scenarios;
    }

    public function deserialize(): void
    {
        $this->id = Uuid::uuid4();

        foreach ($this->scenarioMenaces as $scenario) {
            $scenario->deserialize();
            $scenario->setModeleAnalyse($this);
            foreach ($scenario->getMesuresProtections() as $mesure) {
                $mesure->addScenarioMenace($scenario);
            }

            $scenario->setMesuresProtections([]);
        }
        foreach ($this->questionConformites as $question) {
            $question->deserialize();
            $question->setModeleAnalyse($this);
        }
        foreach ($this->criterePrincipeFondamentaux as $critere) {
            $critere->deserialize();
            $critere->setModeleAnalyse($this);
        }
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function __toString(): string
    {
        if (\is_null($this->getNom())) {
            return '';
        }

        if (\mb_strlen($this->getNom()) > 85) {
            return \mb_substr($this->getNom(), 0, 85) . '...';
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

    public function setOptionRightSelection($optionRightSelection)
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
