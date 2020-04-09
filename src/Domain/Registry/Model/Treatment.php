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
     * FR: Évaluation ou notation (traitement spécifique).
     *
     * @var bool
     */
    private $evaluationOrRating;

    /**
     * FR: Décisions automatisées  avec  effet  juridique (traitement spécifique).
     *
     * @var bool
     */
    private $automatedDecisionsWithLegalEffect;

    /**
     * FR: Exclusion automatique d'un service (traitement spécifique).
     *
     * @var bool
     */
    private $automaticExclusionService;

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
        $this->id                                = Uuid::uuid4();
        $this->paperProcessing                   = false;
        $this->concernedPeople                   = [];
        $this->dataCategories                    = [];
        $this->contractors                       = [];
        $this->delay                             = new Delay();
        $this->securityAccessControl             = new ComplexChoice();
        $this->securityTracability               = new ComplexChoice();
        $this->securitySaving                    = new ComplexChoice();
        $this->securityUpdate                    = new ComplexChoice();
        $this->securityOther                     = new ComplexChoice();
        $this->systematicMonitoring              = false;
        $this->largeScaleCollection              = false;
        $this->vulnerablePeople                  = false;
        $this->dataCrossing                      = false;
        $this->evaluationOrRating                = false;
        $this->automatedDecisionsWithLegalEffect = false;
        $this->automaticExclusionService         = false;
        $this->active                            = true;
        $this->completion                        = 0;
        $this->template                          = false;
        $this->proofs                            = [];
    }

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

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getGoal(): ?string
    {
        return $this->goal;
    }

    public function setGoal(?string $goal): void
    {
        $this->goal = $goal;
    }

    public function getManager(): ?string
    {
        return $this->manager;
    }

    public function setManager(?string $manager): void
    {
        $this->manager = $manager;
    }

    public function getSoftware(): ?string
    {
        return $this->software;
    }

    public function setSoftware(?string $software): void
    {
        $this->software = $software;
    }

    public function isPaperProcessing(): bool
    {
        return $this->paperProcessing;
    }

    public function setPaperProcessing(bool $paperProcessing): void
    {
        $this->paperProcessing = $paperProcessing;
    }

    public function getLegalBasis(): ?string
    {
        return $this->legalBasis;
    }

    public function setLegalBasis(?string $legalBasis): void
    {
        $this->legalBasis = $legalBasis;
    }

    public function getLegalBasisJustification(): ?string
    {
        return $this->legalBasisJustification;
    }

    public function setLegalBasisJustification(?string $legalBasisJustification): void
    {
        $this->legalBasisJustification = $legalBasisJustification;
    }

    public function getObservation(): ?string
    {
        return $this->observation;
    }

    public function setObservation(?string $observation): void
    {
        $this->observation = $observation;
    }

    public function getConcernedPeople(): array
    {
        return $this->concernedPeople;
    }

    public function setConcernedPeople(array $concernedPeople): void
    {
        $this->concernedPeople = $concernedPeople;
    }

    public function getDataCategories(): iterable
    {
        return $this->dataCategories;
    }

    public function setDataCategories(iterable $dataCategories): void
    {
        $this->dataCategories = $dataCategories;
    }

    public function getDataCategoryOther(): ?string
    {
        return $this->dataCategoryOther;
    }

    public function setDataCategoryOther(?string $dataCategoryOther): void
    {
        $this->dataCategoryOther = $dataCategoryOther;
    }

    public function getDataOrigin(): ?string
    {
        return $this->dataOrigin;
    }

    public function setDataOrigin(?string $dataOrigin): void
    {
        $this->dataOrigin = $dataOrigin;
    }

    public function getRecipientCategory(): ?string
    {
        return $this->recipientCategory;
    }

    public function setRecipientCategory(?string $recipientCategory): void
    {
        $this->recipientCategory = $recipientCategory;
    }

    public function addContractor(Contractor $contractor): void
    {
        $contractor->addTreatment($this);
        $this->contractors[] = $contractor;
    }

    public function removeContractor(Contractor $contractor): void
    {
        $contractor->removeTreatment($this);

        $key = \array_search($contractor, $this->contractors, true);

        if (false === $key) {
            return;
        }

        unset($this->contractors[$key]);
    }

    public function getContractors(): iterable
    {
        return $this->contractors;
    }

    public function getDelay(): Delay
    {
        return $this->delay;
    }

    public function setDelay(Delay $delay): void
    {
        $this->delay = $delay;
    }

    public function getSecurityAccessControl(): ComplexChoice
    {
        return $this->securityAccessControl;
    }

    public function setSecurityAccessControl(ComplexChoice $securityAccessControl): void
    {
        $this->securityAccessControl = $securityAccessControl;
    }

    public function getSecurityTracability(): ComplexChoice
    {
        return $this->securityTracability;
    }

    public function setSecurityTracability(ComplexChoice $securityTracability): void
    {
        $this->securityTracability = $securityTracability;
    }

    public function getSecuritySaving(): ComplexChoice
    {
        return $this->securitySaving;
    }

    public function setSecuritySaving(ComplexChoice $securitySaving): void
    {
        $this->securitySaving = $securitySaving;
    }

    public function getSecurityUpdate(): ComplexChoice
    {
        return $this->securityUpdate;
    }

    public function setSecurityUpdate(ComplexChoice $securityUpdate): void
    {
        $this->securityUpdate = $securityUpdate;
    }

    public function getSecurityOther(): ComplexChoice
    {
        return $this->securityOther;
    }

    public function setSecurityOther(ComplexChoice $securityOther): void
    {
        $this->securityOther = $securityOther;
    }

    public function isSystematicMonitoring(): bool
    {
        return $this->systematicMonitoring;
    }

    public function setSystematicMonitoring(bool $systematicMonitoring): void
    {
        $this->systematicMonitoring = $systematicMonitoring;
    }

    public function isLargeScaleCollection(): bool
    {
        return $this->largeScaleCollection;
    }

    public function setLargeScaleCollection(bool $largeScaleCollection): void
    {
        $this->largeScaleCollection = $largeScaleCollection;
    }

    public function isVulnerablePeople(): bool
    {
        return $this->vulnerablePeople;
    }

    public function setVulnerablePeople(bool $vulnerablePeople): void
    {
        $this->vulnerablePeople = $vulnerablePeople;
    }

    public function isDataCrossing(): bool
    {
        return $this->dataCrossing;
    }

    public function setDataCrossing(bool $dataCrossing): void
    {
        $this->dataCrossing = $dataCrossing;
    }

    public function getAuthorizedPeople(): ?string
    {
        return $this->authorizedPeople;
    }

    public function setAuthorizedPeople(?string $authorizedPeople): void
    {
        $this->authorizedPeople = $authorizedPeople;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getCompletion(): int
    {
        return $this->completion;
    }

    public function setCompletion(int $completion): void
    {
        $this->completion = $completion;
    }

    public function isTemplate(): bool
    {
        return $this->template;
    }

    public function setTemplate(bool $template): void
    {
        $this->template = $template;
    }

    public function getTemplateIdentifier(): ?int
    {
        return $this->templateIdentifier;
    }

    public function setTemplateIdentifier(?int $templateIdentifier): void
    {
        $this->templateIdentifier = $templateIdentifier;
    }

    public function getProofs(): iterable
    {
        return $this->proofs;
    }

    public function getClonedFrom(): ?Treatment
    {
        return $this->clonedFrom;
    }

    public function setClonedFrom(?Treatment $clonedFrom): void
    {
        $this->clonedFrom = $clonedFrom;
    }

    public function isEvaluationOrRating(): bool
    {
        return $this->evaluationOrRating;
    }

    public function setEvaluationOrRating(bool $evaluationOrRating): void
    {
        $this->evaluationOrRating = $evaluationOrRating;
    }

    public function isAutomatedDecisionsWithLegalEffect(): bool
    {
        return $this->automatedDecisionsWithLegalEffect;
    }

    public function setAutomatedDecisionsWithLegalEffect(bool $automatedDecisionsWithLegalEffect): void
    {
        $this->automatedDecisionsWithLegalEffect = $automatedDecisionsWithLegalEffect;
    }

    public function isAutomaticExclusionService(): bool
    {
        return $this->automaticExclusionService;
    }

    public function setAutomaticExclusionService(bool $automaticExclusionService): void
    {
        $this->automaticExclusionService = $automaticExclusionService;
    }
}
