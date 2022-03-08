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
use App\Application\Traits\Model\SoftDeletableTrait;
use App\Domain\Reporting\Model\LoggableSubject;
use App\Domain\User\Model\Service;
use App\Domain\User\Model\User;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Violation implements LoggableSubject, CollectivityRelated
{
    use CollectivityTrait;
    use CreatorTrait;
    use HistoryTrait;
    use SoftDeletableTrait;

    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var \DateTime|null
     */
    private $date;

    /**
     * @var bool
     */
    private $inProgress;

    /**
     * @var string|null
     */
    private $violationNature;

    /**
     * @var iterable
     */
    private $origins;

    /**
     * @var string|null
     */
    private $cause;

    /**
     * @var iterable
     */
    private $concernedDataNature;

    /**
     * @var iterable
     */
    private $concernedPeopleCategories;

    /**
     * @var int|null
     */
    private $nbAffectedRows;

    /**
     * @var int|null
     */
    private $nbAffectedPersons;

    /**
     * @var iterable
     */
    private $potentialImpactsNature;

    /**
     * @var string|null
     */
    private $gravity;

    /**
     * @var string|null
     */
    private $communication;

    /**
     * @var string|null
     */
    private $communicationPrecision;

    /**
     * @var string|null
     */
    private $appliedMeasuresAfterViolation;

    /**
     * @var string|null
     */
    private $notification;

    /**
     * @var string|null
     */
    private $notificationDetails;

    /**
     * @var string|null
     */
    private $comment;

    /**
     * @var iterable
     */
    private $proofs;

    /**
     * @var Service|null
     */
    private $service;

    private Collection $mesurements;
    private Collection $treatments;

    /**
     * Violation constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id                        = Uuid::uuid4();
        $this->date                      = new \DateTime();
        $this->inProgress                = false;
        $this->origins                   = [];
        $this->concernedDataNature       = [];
        $this->concernedPeopleCategories = [];
        $this->potentialImpactsNature    = [];
        $this->proofs                    = [];
        $this->treatments                = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function __toString(): string
    {
        if (\is_null($this->getDate())) {
            return '';
        }

        return "Violation du {$this->getDate()->format('d/m/Y')}";
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(?\DateTime $date): void
    {
        $this->date = $date;
    }

    public function isInProgress(): bool
    {
        return $this->inProgress;
    }

    public function setInProgress(bool $inProgress): void
    {
        $this->inProgress = $inProgress;
    }

    public function getViolationNature(): ?string
    {
        return $this->violationNature;
    }

    public function setViolationNature(?string $violationNature): void
    {
        $this->violationNature = $violationNature;
    }

    public function getOrigins(): iterable
    {
        return $this->origins;
    }

    public function setOrigins(iterable $origins): void
    {
        $this->origins = $origins;
    }

    public function getCause(): ?string
    {
        return $this->cause;
    }

    public function setCause(?string $cause): void
    {
        $this->cause = $cause;
    }

    public function getConcernedDataNature(): iterable
    {
        return $this->concernedDataNature;
    }

    public function setConcernedDataNature(iterable $concernedDataNature): void
    {
        $this->concernedDataNature = $concernedDataNature;
    }

    public function getConcernedPeopleCategories(): iterable
    {
        return $this->concernedPeopleCategories;
    }

    public function setConcernedPeopleCategories(iterable $concernedPeopleCategories): void
    {
        $this->concernedPeopleCategories = $concernedPeopleCategories;
    }

    public function getNbAffectedRows(): ?int
    {
        return $this->nbAffectedRows;
    }

    public function setNbAffectedRows(?int $nbAffectedRows): void
    {
        $this->nbAffectedRows = $nbAffectedRows;
    }

    public function getNbAffectedPersons(): ?int
    {
        return $this->nbAffectedPersons;
    }

    public function setNbAffectedPersons(?int $nbAffectedPersons): void
    {
        $this->nbAffectedPersons = $nbAffectedPersons;
    }

    public function getPotentialImpactsNature(): iterable
    {
        return $this->potentialImpactsNature;
    }

    public function setPotentialImpactsNature(iterable $potentialImpactsNature): void
    {
        $this->potentialImpactsNature = $potentialImpactsNature;
    }

    public function getGravity(): ?string
    {
        return $this->gravity;
    }

    public function setGravity(?string $gravity): void
    {
        $this->gravity = $gravity;
    }

    public function getCommunication(): ?string
    {
        return $this->communication;
    }

    public function setCommunication(?string $communication): void
    {
        $this->communication = $communication;
    }

    public function getCommunicationPrecision(): ?string
    {
        return $this->communicationPrecision;
    }

    public function setCommunicationPrecision(?string $communicationPrecision): void
    {
        $this->communicationPrecision = $communicationPrecision;
    }

    public function getAppliedMeasuresAfterViolation(): ?string
    {
        return $this->appliedMeasuresAfterViolation;
    }

    public function setAppliedMeasuresAfterViolation(?string $appliedMeasuresAfterViolation): void
    {
        $this->appliedMeasuresAfterViolation = $appliedMeasuresAfterViolation;
    }

    public function getNotification(): ?string
    {
        return $this->notification;
    }

    public function setNotification(?string $notification): void
    {
        $this->notification = $notification;
    }

    public function getNotificationDetails(): ?string
    {
        return $this->notificationDetails;
    }

    public function setNotificationDetails(?string $notificationDetails): void
    {
        $this->notificationDetails = $notificationDetails;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    public function getProofs(): iterable
    {
        return $this->proofs;
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

    public function getMesurements(): Collection
    {
        return $this->mesurements;
    }

    public function setMesurement(Collection $mesurements): void
    {
        $this->mesurements = $mesurements;
    }
    public function addTreatment(Treatment $treatment): void
    {
        $this->treatments[] = $treatment;
    }

    public function removeTreatment(Treatment $treatment): void
    {
        if ($this->treatments && $this->treatments->count() && $this->treatments->contains($treatment)) {
            $this->treatments->removeElement($treatment);
        }
    }

    public function getTreatments(): iterable
    {
        return $this->treatments;
    }
}
