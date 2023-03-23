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

use App\Application\Interfaces\CollectivityRelated;
use App\Application\Traits\Model\CollectivityTrait;
use App\Application\Traits\Model\CreatorTrait;
use App\Application\Traits\Model\HistoryTrait;
use App\Domain\Registry\Model\ConformiteTraitement\ConformiteTraitement;
use App\Domain\Registry\Model\Embeddable\ComplexChoice;
use App\Domain\Reporting\Model\LoggableSubject;
use App\Domain\User\Model\Service;
use App\Domain\User\Model\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Treatment implements LoggableSubject, CollectivityRelated
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
     * @var bool
     */
    private $public = false;

    /**
     * @var bool
     */
    private $exempt_AIPD = false;

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
     * FR: Logiciels et supports.
     *
     * @var Tool[]|iterable
     */
    private $tools;

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
     * @var iterable
     */
    private $dataCategories;

    /**
     * @var ShelfLife[]|iterable
     */
    private $shelfLifes;

    /**
     * FR: Autres catégories.
     *
     * @var string|null
     */
    private $dataCategoryOther;

    private $software;

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
     * FR: Je suis en capacité de ressortir les personnes habilitées (mesure de sécurité).
     *
     * @var bool
     */
    private $securityEntitledPersons;

    /**
     * FR: La personne ou la procédure qui permet d’ouvrir des comptes est clairement identifiée (mesure de sécurité).
     *
     * @var bool
     */
    private $securityOpenAccounts;

    /**
     * FR: Les spécificités de sensibilisation liées à ce traitement sont délivrées (mesure de sécurité).
     *
     * @var bool
     */
    private $securitySpecificitiesDelivered;

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
     * FR: Usage innovant (traitement spécifique).
     *
     * @var bool
     */
    private $innovativeUse;

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
     * FR: Particuliers (Personnes concernées).
     *
     * @var ComplexChoice
     */
    private $concernedPeopleParticular;

    /**
     * FR: Usagers (Personnes concernées).
     *
     * @var ComplexChoice
     */
    private $concernedPeopleUsager;

    /**
     * FR: Internautes (Personnes concernées).
     *
     * @var ComplexChoice
     */
    private $concernedPeopleUser;

    /**
     * FR: Salariés (Personnes concernées).
     *
     * @var ComplexChoice
     */
    private $concernedPeopleAgent;

    /**
     * FR: Elus (Personnes concernées).
     *
     * @var ComplexChoice
     */
    private $concernedPeopleElected;

    /**
     * FR: Professionnels (Personnes concernées).
     *
     * @var ComplexChoice
     */
    private $concernedPeopleCompany;

    /**
     * FR: Partenaires (Personnes concernées).
     *
     * @var ComplexChoice
     */
    private $concernedPeoplePartner;

    /**
     * FR: Autres (Personnes concernées).
     *
     * @var ComplexChoice
     */
    private $concernedPeopleOther;

    /**
     * FR: En tant que (Informations générales).
     *
     * @var string|null
     */
    private $author;

    /**
     * @var string|null
     */
    private $coordonneesResponsableTraitement;

    /**
     * FR: Moyens de la collecte des données (Détails).
     *
     * @var array|null
     */
    private $collectingMethod;

    /**
     * FR: Estimation du nombre de personnes (Détails).
     *
     * @var int|null
     */
    private $estimatedConcernedPeople;

    /**
     * @var ConformiteTraitement|null
     */
    private $conformiteTraitement;

    /**
     * @var Service|null
     */
    private $service;

    /**
     * FR: Préconisation DPO.
     *
     * @var string|null
     */
    private $dpoMessage;

    /**
     * FR: Autre moyen de collecte.
     *
     * @var string|null
     */
    private $otherCollectingMethod;

    private Collection $mesurements;
    private Collection $requests;

    private Collection $violations;

    /**
     *  Mentions légales apposées.
     *
     * @var bool
     */
    private $legalMentions;

    /**
     * Consentement demandé.
     *
     * @var bool
     */
    private $consentRequest;

    /**
     * Format de la demande du consentement.
     *
     * @var string|null
     */
    private $consentRequestFormat;

    /**
     * Treatment constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id                                = Uuid::uuid4();
        $this->paperProcessing                   = false;
        $this->shelfLifes                        = [];
        $this->dataCategories                    = [];
        $this->contractors                       = [];
        $this->securityAccessControl             = new ComplexChoice();
        $this->securityTracability               = new ComplexChoice();
        $this->securitySaving                    = new ComplexChoice();
        $this->securityUpdate                    = new ComplexChoice();
        $this->securityOther                     = new ComplexChoice();
        $this->securityEntitledPersons           = false;
        $this->securityOpenAccounts              = false;
        $this->securitySpecificitiesDelivered    = false;
        $this->systematicMonitoring              = false;
        $this->largeScaleCollection              = false;
        $this->vulnerablePeople                  = false;
        $this->dataCrossing                      = false;
        $this->evaluationOrRating                = false;
        $this->automatedDecisionsWithLegalEffect = false;
        $this->automaticExclusionService         = false;
        $this->innovativeUse                     = false;
        $this->active                            = true;
        $this->completion                        = 0;
        $this->template                          = false;
        $this->proofs                            = [];
        $this->concernedPeopleParticular         = new ComplexChoice();
        $this->concernedPeopleUser               = new ComplexChoice();
        $this->concernedPeopleUsager             = new ComplexChoice();
        $this->concernedPeopleAgent              = new ComplexChoice();
        $this->concernedPeopleElected            = new ComplexChoice();
        $this->concernedPeopleCompany            = new ComplexChoice();
        $this->concernedPeoplePartner            = new ComplexChoice();
        $this->concernedPeopleOther              = new ComplexChoice();
        $this->requests                          = new ArrayCollection();
        $this->violations                        = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function __toString(): string
    {
        if (\is_null($this->getName())) {
            return '';
        }

        if (\mb_strlen($this->getName()) > 85) {
            return \mb_substr($this->getName(), 0, 85) . '...';
        }

        return $this->getName();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(?bool $public): void
    {
        $this->public = $public;
    }

    public function getExemptAIPD(): ?bool
    {
        return $this->exempt_AIPD;
    }

    public function setExemptAIPD(?bool $exempt_AIPD): void
    {
        $this->exempt_AIPD = $exempt_AIPD;
    }

    public function isExemptAIPD(): bool
    {
        return $this->exempt_AIPD;
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

    public function getTools(): ?iterable
    {
        return $this->tools;
    }

    public function getToolsString(): ?string
    {
        $data = $this->getTools();
        if (!$this->getCollectivity()->isHasModuleTools()) {
            return $this->getSoftware();
        }
        if (is_null($data)) {
            return null;
        }
        if (is_object($data) && method_exists($data, 'toArray')) {
            $data = $data->toArray();
        }

        return join(', ', array_map(function ($object) {
            return $object->getName();
        }, (array) $data));
    }

    public function setTools(?iterable $tools): void
    {
        $this->tools = $tools;
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

    public function getDataCategories(): iterable
    {
        return $this->dataCategories;
    }

    public function setDataCategories(iterable $dataCategories): void
    {
        $this->dataCategories = $dataCategories;
    }

    public function getShelfLifes(): iterable
    {
        return $this->shelfLifes;
    }

    public function setShelfLifes(iterable $shelfLifes): void
    {
        $this->shelfLifes = $shelfLifes;
    }

    public function addShelfLife(ShelfLife $shelfLife): void
    {
        $this->shelfLifes[] = $shelfLife;
        $shelfLife->setTreatment($this);
    }

    public function removeShelfLife(ShelfLife $shelfLife)
    {
        $key = \array_search($shelfLife, iterable_to_array($this->shelfLifes), true);

        if (false === $key) {
            return;
        }

        unset($this->shelfLifes[$key]);
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

    public function getSoftware(): ?string
    {
        return $this->software;
    }

    public function setSoftware(?string $software): void
    {
        $this->software = $software;
    }

    public function isAutomaticExclusionService(): bool
    {
        return $this->automaticExclusionService;
    }

    public function setAutomaticExclusionService(bool $automaticExclusionService): void
    {
        $this->automaticExclusionService = $automaticExclusionService;
    }

    public function getConcernedPeopleParticular(): ComplexChoice
    {
        return $this->concernedPeopleParticular;
    }

    public function setConcernedPeopleParticular(ComplexChoice $concernedPeopleParticular): void
    {
        $this->concernedPeopleParticular = $concernedPeopleParticular;
    }

    public function getConcernedPeopleUser(): ComplexChoice
    {
        return $this->concernedPeopleUser;
    }

    public function setConcernedPeopleUser(ComplexChoice $concernedPeopleUser): void
    {
        $this->concernedPeopleUser = $concernedPeopleUser;
    }

    public function getConcernedPeopleUsager(): ComplexChoice
    {
        return $this->concernedPeopleUsager;
    }

    public function setConcernedPeopleUsager(ComplexChoice $concernedPeopleUsager): void
    {
        $this->concernedPeopleUsager = $concernedPeopleUsager;
    }

    public function getConcernedPeopleAgent(): ComplexChoice
    {
        return $this->concernedPeopleAgent;
    }

    public function setConcernedPeopleAgent(ComplexChoice $concernedPeopleAgent): void
    {
        $this->concernedPeopleAgent = $concernedPeopleAgent;
    }

    public function getConcernedPeopleElected(): ComplexChoice
    {
        return $this->concernedPeopleElected;
    }

    public function setConcernedPeopleElected(ComplexChoice $concernedPeopleElected): void
    {
        $this->concernedPeopleElected = $concernedPeopleElected;
    }

    public function getConcernedPeopleCompany(): ComplexChoice
    {
        return $this->concernedPeopleCompany;
    }

    public function setConcernedPeopleCompany(ComplexChoice $concernedPeopleCompany): void
    {
        $this->concernedPeopleCompany = $concernedPeopleCompany;
    }

    public function getConcernedPeoplePartner(): ComplexChoice
    {
        return $this->concernedPeoplePartner;
    }

    public function setConcernedPeoplePartner(ComplexChoice $concernedPeoplePartner): void
    {
        $this->concernedPeoplePartner = $concernedPeoplePartner;
    }

    public function getConcernedPeopleOther(): ComplexChoice
    {
        return $this->concernedPeopleOther;
    }

    public function setConcernedPeopleOther(ComplexChoice $concernedPeopleOther): void
    {
        $this->concernedPeopleOther = $concernedPeopleOther;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): void
    {
        $this->author = $author;
    }

    public function getCoordonneesResponsableTraitement(): ?string
    {
        return $this->coordonneesResponsableTraitement;
    }

    public function setCoordonneesResponsableTraitement(?string $coordonneesResponsableTraitement): void
    {
        $this->coordonneesResponsableTraitement = $coordonneesResponsableTraitement;
    }

    public function getCollectingMethod(): ?array
    {
        return $this->collectingMethod;
    }

    public function setCollectingMethod($collectingMethod): void
    {
        $this->collectingMethod = $collectingMethod;
    }

    public function getEstimatedConcernedPeople(): ?int
    {
        return $this->estimatedConcernedPeople;
    }

    public function setEstimatedConcernedPeople(?int $estimatedConcernedPeople): void
    {
        $this->estimatedConcernedPeople = $estimatedConcernedPeople;
    }

    public function isSecurityEntitledPersons(): bool
    {
        return $this->securityEntitledPersons;
    }

    public function setSecurityEntitledPersons(bool $securityEntitledPersons): void
    {
        $this->securityEntitledPersons = $securityEntitledPersons;
    }

    public function isSecurityOpenAccounts(): bool
    {
        return $this->securityOpenAccounts;
    }

    public function setSecurityOpenAccounts(bool $securityOpenAccounts): void
    {
        $this->securityOpenAccounts = $securityOpenAccounts;
    }

    public function isSecuritySpecificitiesDelivered(): bool
    {
        return $this->securitySpecificitiesDelivered;
    }

    public function setSecuritySpecificitiesDelivered(bool $securitySpecificitiesDelivered): void
    {
        $this->securitySpecificitiesDelivered = $securitySpecificitiesDelivered;
    }

    public function isInnovativeUse(): bool
    {
        return $this->innovativeUse;
    }

    public function setInnovativeUse(bool $innovativeUse): void
    {
        $this->innovativeUse = $innovativeUse;
    }

    public function getConformiteTraitement(): ?ConformiteTraitement
    {
        return $this->conformiteTraitement;
    }

    public function setConformiteTraitement(ConformiteTraitement $conformiteTraitement = null): void
    {
        $this->conformiteTraitement = $conformiteTraitement;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(Service $service = null): void
    {
        $this->service = $service;
    }

    public function isInUserServices(User $user): bool
    {
        if (false == $user->getCollectivity()->getIsServicesEnabled()) {
            return true;
        }

        $result = false;

        foreach ($user->getServices() as $service) {
            if ($this->getService() && $service->getId() == $this->getService()->getId()) {
                $result = true;
            }
        }

        return $result;
    }

    public function getDpoMessage(): ?string
    {
        return $this->dpoMessage;
    }

    public function setDpoMessage(?string $dpoMessage): void
    {
        $this->dpoMessage = $dpoMessage;
    }

    public function getOtherCollectingMethod(): ?string
    {
        return $this->otherCollectingMethod;
    }

    public function setOtherCollectingMethod(?string $otherCollectingMethod): void
    {
        $this->otherCollectingMethod = $otherCollectingMethod;
    }

    public function getMesurements(): Collection
    {
        return $this->mesurements;
    }

    public function setMesurement(Collection $mesurements): void
    {
        $this->mesurements = $mesurements;
    }

    public function addRequest(Request $request): void
    {
        $request->addTreatment($this);
        $this->requests[] = $request;
    }

    public function removeRequest(Request $request): void
    {
        $request->removeTreatment($this);

        if ($this->requests && $this->requests->count() && $this->requests->contains($request)) {
            $this->requests->removeElement($request);
        }
    }

    public function getRequests(): Collection
    {
        return $this->requests;
    }

    public function setRequests(Collection $requests)
    {
        $this->requests = $requests;
    }

    public function addViolation(Violation $violation): void
    {
        $violation->addTreatment($this);
        $this->violations[] = $violation;
    }

    public function removeViolation(Violation $violation): void
    {
        $violation->removeTreatment($this);

        $key = \array_search($violation, $this->violations, true);

        if (false === $key) {
            return;
        }

        unset($this->violations[$key]);
    }

    public function getViolations()
    {
        return $this->violations;
    }

    public function getLegalMentions(): ?bool
    {
        return $this->legalMentions;
    }

    public function setLegalMentions(?bool $legalMentions): void
    {
        $this->legalMentions = $legalMentions;
    }

    public function getConsentRequest(): ?bool
    {
        return $this->consentRequest;
    }

    public function setConsentRequest(?bool $consentRequest): void
    {
        $this->consentRequest = $consentRequest;
    }

    public function getConsentRequestFormat(): ?string
    {
        return $this->consentRequestFormat;
    }

    public function setConsentRequestFormat(?string $consentRequestFormat): void
    {
        $this->consentRequestFormat = $consentRequestFormat;
    }
}
