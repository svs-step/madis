<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author Donovan Bourlard <donovan@awkan.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace App\Domain\Registry\Model;

use App\Application\Traits\Model\CollectivityTrait;
use App\Application\Traits\Model\CreatorTrait;
use App\Application\Traits\Model\HistoryTrait;
use App\Domain\Registry\Model\Embeddable\ComplexChoice;
use App\Domain\Registry\Model\Embeddable\Delay;
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
     * @var string|null
     */
    private $name;

    /**
     * FR: Finalités (Objectif).
     *
     * @var string|null
     */
    private $goal;

    /**
     * FR: Gestionnaire.
     *
     * @var string|null
     */
    private $manager;

    /**
     * FR: Logiciel.
     *
     * @var string|null
     */
    private $software;

    /**
     * FR: Traitement papier.
     *
     * @var bool
     */
    private $paperProcessing;

    /**
     * FR: Base légale du traitement.
     *
     * @var string|null
     */
    private $legalBasis;

    /**
     * FR: Justification de la base légale.
     *
     * @var string|null
     */
    private $legalBasisJustification;

    /**
     * @var string|null
     */
    private $observation;

    /**
     * FR: Personnes concernées.
     *
     * @var array
     */
    private $concernedPeople;

    /**
     * @var iterable
     */
    private $dataCategories;

    /**
     * FR: Autres catégories.
     *
     * @var string|null
     */
    private $dataCategoryOther;

    /**
     * FR: Origine des données.
     *
     * @var string|null
     */
    private $dataOrigin;

    /**
     * FR: Destinataire des données.
     *
     * @var string|null
     */
    private $recipientCategory;

    /**
     * FR: Sous traitants.
     *
     * @var iterable
     */
    private $contractors;

    /**
     * @var Delay
     */
    private $delay;

    /**
     * FR: Contrôle d'accès (mesure de sécurité).
     *
     * @var ComplexChoice
     */
    private $securityAccessControl;

    /**
     * FR: Tracabilité (mesure de sécurité).
     *
     * @var ComplexChoice
     */
    private $securityTracability;

    /**
     * FR: Sauvegarde (mesure de sécurité).
     *
     * @var ComplexChoice
     */
    private $securitySaving;

    /**
     * FR: Mises à jour (mesure de sécurité).
     *
     * @var ComplexChoice
     */
    private $securityUpdate;

    /**
     * FR: Autres (mesure de sécurité).
     *
     * @var ComplexChoice
     */
    private $securityOther;

    /**
     * FR: Surveillance systématique (traitement spécifique).
     *
     * @var bool
     */
    private $systematicMonitoring;

    /**
     * FR: Collecte à grande échelle (traitement spécifique).
     *
     * @var bool
     */
    private $largeScaleCollection;

    /**
     * FR: Personnes vulnérables (traitement spécifique).
     *
     * @var bool
     */
    private $vulnerablePeople;

    /**
     * FR: Croisement de données (traitement spécifique).
     *
     * @var bool
     */
    private $dataCrossing;

    /**
     * FR: Personnes habilitées.
     *
     * @var string|null
     */
    private $authorizedPeople;

    /**
     * @var bool
     */
    private $active;

    /**
     * @var int
     */
    private $completion;

    /**
     * Please note that this variable goal is only for Database queries, no need to use it in code.
     *
     * @var bool
     */
    private $template;

    /**
     * Please note that this variable goal is only for Database queries, no need to use it in code.
     *
     * @var int|null
     */
    private $templateIdentifier;

    /**
     * @var iterable
     */
    private $proofs;

    /**
     * @var Treatment|null
     */
    private $clonedFrom;

    /**
     * Treatment constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id                    = Uuid::uuid4();
        $this->paperProcessing       = false;
        $this->concernedPeople       = [];
        $this->dataCategories        = [];
        $this->contractors           = [];
        $this->delay                 = new Delay();
        $this->securityAccessControl = new ComplexChoice();
        $this->securityTracability   = new ComplexChoice();
        $this->securitySaving        = new ComplexChoice();
        $this->securityUpdate        = new ComplexChoice();
        $this->securityOther         = new ComplexChoice();
        $this->systematicMonitoring  = false;
        $this->largeScaleCollection  = false;
        $this->vulnerablePeople      = false;
        $this->dataCrossing          = false;
        $this->active                = true;
        $this->completion            = 0;
        $this->template              = false;
        $this->proofs                = [];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if (\is_null($this->getName())) {
            return '';
        }

        if (\mb_strlen($this->getName()) > 50) {
            return \mb_substr($this->getName(), 0, 50) . '...';
        }

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
     * @param string|null $name
     */
    public function setName(?string $name): void
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
     * @param string|null $goal
     */
    public function setGoal(?string $goal): void
    {
        $this->goal = $goal;
    }

    /**
     * @return string|null
     */
    public function getManager(): ?string
    {
        return $this->manager;
    }

    /**
     * @param string|null $manager
     */
    public function setManager(?string $manager): void
    {
        $this->manager = $manager;
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
     * @return bool
     */
    public function isPaperProcessing(): bool
    {
        return $this->paperProcessing;
    }

    /**
     * @param bool $paperProcessing
     */
    public function setPaperProcessing(bool $paperProcessing): void
    {
        $this->paperProcessing = $paperProcessing;
    }

    /**
     * @return string|null
     */
    public function getLegalBasis(): ?string
    {
        return $this->legalBasis;
    }

    /**
     * @param string|null $legalBasis
     */
    public function setLegalBasis(?string $legalBasis): void
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
     * @return string|null
     */
    public function getObservation(): ?string
    {
        return $this->observation;
    }

    /**
     * @param string|null $observation
     */
    public function setObservation(?string $observation): void
    {
        $this->observation = $observation;
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
     * @return iterable
     */
    public function getDataCategories(): iterable
    {
        return $this->dataCategories;
    }

    /**
     * @param iterable $dataCategories
     */
    public function setDataCategories(iterable $dataCategories): void
    {
        $this->dataCategories = $dataCategories;
    }

    /**
     * @return string|null
     */
    public function getDataCategoryOther(): ?string
    {
        return $this->dataCategoryOther;
    }

    /**
     * @param string|null $dataCategoryOther
     */
    public function setDataCategoryOther(?string $dataCategoryOther): void
    {
        $this->dataCategoryOther = $dataCategoryOther;
    }

    /**
     * @return string|null
     */
    public function getDataOrigin(): ?string
    {
        return $this->dataOrigin;
    }

    /**
     * @param string|null $dataOrigin
     */
    public function setDataOrigin(?string $dataOrigin): void
    {
        $this->dataOrigin = $dataOrigin;
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
        $this->contractors[] = $contractor;
    }

    /**
     * @param Contractor $contractor
     */
    public function removeContractor(Contractor $contractor): void
    {
        $contractor->removeTreatment($this);

        $key = \array_search($contractor, $this->contractors, true);

        if (false === $key) {
            return;
        }

        unset($this->contractors[$key]);
    }

    /**
     * @return iterable
     */
    public function getContractors(): iterable
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
     * @return ComplexChoice
     */
    public function getSecurityAccessControl(): ComplexChoice
    {
        return $this->securityAccessControl;
    }

    /**
     * @param ComplexChoice $securityAccessControl
     */
    public function setSecurityAccessControl(ComplexChoice $securityAccessControl): void
    {
        $this->securityAccessControl = $securityAccessControl;
    }

    /**
     * @return ComplexChoice
     */
    public function getSecurityTracability(): ComplexChoice
    {
        return $this->securityTracability;
    }

    /**
     * @param ComplexChoice $securityTracability
     */
    public function setSecurityTracability(ComplexChoice $securityTracability): void
    {
        $this->securityTracability = $securityTracability;
    }

    /**
     * @return ComplexChoice
     */
    public function getSecuritySaving(): ComplexChoice
    {
        return $this->securitySaving;
    }

    /**
     * @param ComplexChoice $securitySaving
     */
    public function setSecuritySaving(ComplexChoice $securitySaving): void
    {
        $this->securitySaving = $securitySaving;
    }

    /**
     * @return ComplexChoice
     */
    public function getSecurityUpdate(): ComplexChoice
    {
        return $this->securityUpdate;
    }

    /**
     * @param ComplexChoice $securityUpdate
     */
    public function setSecurityUpdate(ComplexChoice $securityUpdate): void
    {
        $this->securityUpdate = $securityUpdate;
    }

    /**
     * @return ComplexChoice
     */
    public function getSecurityOther(): ComplexChoice
    {
        return $this->securityOther;
    }

    /**
     * @param ComplexChoice $securityOther
     */
    public function setSecurityOther(ComplexChoice $securityOther): void
    {
        $this->securityOther = $securityOther;
    }

    /**
     * @return bool
     */
    public function isSystematicMonitoring(): bool
    {
        return $this->systematicMonitoring;
    }

    /**
     * @param bool $systematicMonitoring
     */
    public function setSystematicMonitoring(bool $systematicMonitoring): void
    {
        $this->systematicMonitoring = $systematicMonitoring;
    }

    /**
     * @return bool
     */
    public function isLargeScaleCollection(): bool
    {
        return $this->largeScaleCollection;
    }

    /**
     * @param bool $largeScaleCollection
     */
    public function setLargeScaleCollection(bool $largeScaleCollection): void
    {
        $this->largeScaleCollection = $largeScaleCollection;
    }

    /**
     * @return bool
     */
    public function isVulnerablePeople(): bool
    {
        return $this->vulnerablePeople;
    }

    /**
     * @param bool $vulnerablePeople
     */
    public function setVulnerablePeople(bool $vulnerablePeople): void
    {
        $this->vulnerablePeople = $vulnerablePeople;
    }

    /**
     * @return bool
     */
    public function isDataCrossing(): bool
    {
        return $this->dataCrossing;
    }

    /**
     * @param bool $dataCrossing
     */
    public function setDataCrossing(bool $dataCrossing): void
    {
        $this->dataCrossing = $dataCrossing;
    }

    /**
     * @return string|null
     */
    public function getAuthorizedPeople(): ?string
    {
        return $this->authorizedPeople;
    }

    /**
     * @param string|null $authorizedPeople
     */
    public function setAuthorizedPeople(?string $authorizedPeople): void
    {
        $this->authorizedPeople = $authorizedPeople;
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

    /**
     * @return int
     */
    public function getCompletion(): int
    {
        return $this->completion;
    }

    /**
     * @param int $completion
     */
    public function setCompletion(int $completion): void
    {
        $this->completion = $completion;
    }

    /**
     * @return bool
     */
    public function isTemplate(): bool
    {
        return $this->template;
    }

    /**
     * @param bool $template
     */
    public function setTemplate(bool $template): void
    {
        $this->template = $template;
    }

    /**
     * @return int|null
     */
    public function getTemplateIdentifier(): ?int
    {
        return $this->templateIdentifier;
    }

    /**
     * @param int|null $templateIdentifier
     */
    public function setTemplateIdentifier(?int $templateIdentifier): void
    {
        $this->templateIdentifier = $templateIdentifier;
    }

    /**
     * @return iterable
     */
    public function getProofs(): iterable
    {
        return $this->proofs;
    }

    /**
     * @return Treatment|null
     */
    public function getClonedFrom(): ?Treatment
    {
        return $this->clonedFrom;
    }

    /**
     * @param Treatment|null $clonedFrom
     */
    public function setClonedFrom(?Treatment $clonedFrom): void
    {
        $this->clonedFrom = $clonedFrom;
    }
}
