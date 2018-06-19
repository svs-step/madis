<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan.bourlard@outlook.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\Registry\Model;

use App\Application\Traits\Model\CollectivityTrait;
use App\Application\Traits\Model\CreatorTrait;
use App\Application\Traits\Model\HistoryTrait;
use App\Domain\Registry\Model\Embeddable\Delay;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Treatment
{
    use CollectivityTrait;
    use CreatorTrait;
    use HistoryTrait;

    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * FR: Objectif.
     *
     * @var string
     */
    private $goal;

    /**
     * FR: Logiciel.
     *
     * @var string
     */
    private $software;

    /**
     * FR: Base légale du traitement.
     *
     * @var string
     */
    private $legalBasis;

    /**
     * FR: Justification de la base légale.
     *
     * @var string
     */
    private $legalBasisJustification;

    /**
     * FR: Personnes concernées.
     *
     * @var array
     */
    private $concernedPeople;

    /**
     * FR: Catégorie de données.
     *
     * @var string
     */
    private $dataCategory;

    /**
     * FR: Données sensibles.
     *
     * @var bool
     */
    private $sensibleInformations;

    /**
     * FR: Destinataire des données.
     *
     * @var string
     */
    private $recipientCategory;

    /**
     * FR: Sous traitants.
     *
     * @var Collection
     */
    private $contractors;

    /**
     * @var Delay
     */
    private $delay;

    /**
     * @var bool
     */
    private $active;

    public function __construct()
    {
        $this->id                   = Uuid::uuid4();
        $this->concernedPeople      = [];
        $this->sensibleInformations = false;
        $this->contractors          = new ArrayCollection();
        $this->delay                = new Delay();
        $this->active               = true;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getGoal(): ?string
    {
        return $this->goal;
    }

    /**
     * @param string $goal
     */
    public function setGoal(string $goal): void
    {
        $this->goal = $goal;
    }

    /**
     * @return string|null
     */
    public function getSoftware(): ?string
    {
        return $this->software;
    }

    /**
     * @param string|null $software
     */
    public function setSoftware(?string $software): void
    {
        $this->software = $software;
    }

    /**
     * @return string|null
     */
    public function getLegalBasis(): ?string
    {
        return $this->legalBasis;
    }

    /**
     * @param string $legalBasis
     */
    public function setLegalBasis(string $legalBasis): void
    {
        $this->legalBasis = $legalBasis;
    }

    /**
     * @return string|null
     */
    public function getLegalBasisJustification(): ?string
    {
        return $this->legalBasisJustification;
    }

    /**
     * @param string|null $legalBasisJustification
     */
    public function setLegalBasisJustification(?string $legalBasisJustification): void
    {
        $this->legalBasisJustification = $legalBasisJustification;
    }

    /**
     * @return array
     */
    public function getConcernedPeople(): array
    {
        return $this->concernedPeople;
    }

    /**
     * @param array $concernedPeople
     */
    public function setConcernedPeople(array $concernedPeople): void
    {
        $this->concernedPeople = $concernedPeople;
    }

    /**
     * @return string|null
     */
    public function getDataCategory(): ?string
    {
        return $this->dataCategory;
    }

    /**
     * @param string|null $dataCategory
     */
    public function setDataCategory(?string $dataCategory): void
    {
        $this->dataCategory = $dataCategory;
    }

    /**
     * @return bool
     */
    public function isSensibleInformations(): bool
    {
        return $this->sensibleInformations;
    }

    /**
     * @param bool $sensibleInformations
     */
    public function setSensibleInformations(bool $sensibleInformations): void
    {
        $this->sensibleInformations = $sensibleInformations;
    }

    /**
     * @return string|null
     */
    public function getRecipientCategory(): ?string
    {
        return $this->recipientCategory;
    }

    /**
     * @param string|null $recipientCategory
     */
    public function setRecipientCategory(?string $recipientCategory): void
    {
        $this->recipientCategory = $recipientCategory;
    }

    /**
     * @param Contractor $contractor
     */
    public function addContractor(Contractor $contractor): void
    {
        $contractor->addTreatment($this);
        $this->contractors->add($contractor);
    }

    /**
     * @param Contractor $contractor
     */
    public function removeContractor(Contractor $contractor): void
    {
        $contractor->removeTreatment($this);
        $this->contractors->removeElement($contractor);
    }

    /**
     * @return Collection
     */
    public function getContractors(): Collection
    {
        return $this->contractors;
    }

    /**
     * @return Delay
     */
    public function getDelay(): Delay
    {
        return $this->delay;
    }

    /**
     * @param Delay $delay
     */
    public function setDelay(Delay $delay): void
    {
        $this->delay = $delay;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getCompletionPercent()
    {
        $empty = 0;
        $total = 0;

        foreach (\get_object_vars($this) as $var => $value) {
            // Don't count excluded fields
            if (\in_array($var, ['id', 'creator', 'collectivity', 'createdAt', 'updatedAt'])) {
                continue;
            }

            ++$total;
            if (\is_null($value)
                || (\is_array($value) && empty($value))) {
                ++$empty;
            }
        }

        return ($total - $empty) * 100 / $total;
    }
}
