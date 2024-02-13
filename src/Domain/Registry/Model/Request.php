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
use App\Domain\Registry\Model\Embeddable\RequestAnswer;
use App\Domain\Registry\Model\Embeddable\RequestApplicant;
use App\Domain\Registry\Model\Embeddable\RequestConcernedPeople;
use App\Domain\Reporting\Model\LoggableSubject;
use App\Domain\User\Model\Service;
use App\Domain\User\Model\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Request implements LoggableSubject, CollectivityRelated
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
     * @var string|null
     */
    private $object;

    /**
     * @var string|null
     */
    private $otherObject;

    /**
     * @var \DateTime|null
     */
    private $date;

    /**
     * @var string|null
     */
    private $reason;

    /**
     * @var string|null
     */
    private $updatedBy;

    /**
     * @var RequestApplicant|null
     */
    private $applicant;

    /**
     * @var RequestConcernedPeople|null
     */
    private $concernedPeople;

    /**
     * @var bool
     */
    private $complete;

    /**
     * @var bool
     */
    private $legitimateApplicant;

    /**
     * @var bool
     */
    private $legitimateRequest;

    /**
     * @var RequestAnswer|null
     */
    private $answer;

    /**
     * @var iterable
     */
    private $proofs;

    /**
     * @var string|null
     */
    private $state;

    /**
     * @var string|null
     */
    private $stateRejectionReason;

    /**
     * @var Service|null
     */
    private $service;

    private Collection $mesurements;

    private Collection $treatments;

    /**
     * Request constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id                  = Uuid::uuid4();
        $this->date                = new \DateTime();
        $this->applicant           = new RequestApplicant();
        $this->concernedPeople     = new RequestConcernedPeople();
        $this->answer              = new RequestAnswer();
        $this->complete            = false;
        $this->legitimateApplicant = false;
        $this->legitimateRequest   = false;
        $this->proofs              = [];
        $this->treatments          = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function __toString(): string
    {
        if (\is_null($this->getApplicant()->getFirstName())) {
            return '';
        }

        if (\mb_strlen($this->getApplicant()->getFullName()) > 85) {
            return \mb_substr($this->getApplicant()->getFullName(), 0, 85) . '...';
        }

        return $this->getApplicant()->getFullName();
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?string $updatedBy): void
    {
        $this->updatedBy = $updatedBy;
    }

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function setObject(?string $object): void
    {
        $this->object = $object;
    }

    public function getOtherObject(): ?string
    {
        return $this->otherObject;
    }

    public function setOtherObject(?string $otherObject): void
    {
        $this->otherObject = $otherObject;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(?\DateTime $date): void
    {
        $this->date = $date;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): void
    {
        $this->reason = $reason;
    }

    public function getApplicant(): ?RequestApplicant
    {
        return $this->applicant;
    }

    public function setApplicant(?RequestApplicant $applicant): void
    {
        $this->applicant = $applicant;
    }

    public function getConcernedPeople(): ?RequestConcernedPeople
    {
        return $this->concernedPeople;
    }

    public function setConcernedPeople(?RequestConcernedPeople $concernedPeople): void
    {
        $this->concernedPeople = $concernedPeople;
    }

    public function isComplete(): bool
    {
        return $this->complete;
    }

    public function setComplete(bool $complete): void
    {
        $this->complete = $complete;
    }

    public function isLegitimateApplicant(): bool
    {
        return $this->legitimateApplicant;
    }

    public function setLegitimateApplicant(bool $legitimateApplicant): void
    {
        $this->legitimateApplicant = $legitimateApplicant;
    }

    public function isLegitimateRequest(): bool
    {
        return $this->legitimateRequest;
    }

    public function setLegitimateRequest(bool $legitimateRequest): void
    {
        $this->legitimateRequest = $legitimateRequest;
    }

    public function getAnswer(): ?RequestAnswer
    {
        return $this->answer;
    }

    public function setAnswer(?RequestAnswer $answer): void
    {
        $this->answer = $answer;
    }

    public function getProofs(): iterable
    {
        return $this->proofs;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    public function getStateRejectionReason(): ?string
    {
        return $this->stateRejectionReason;
    }

    public function setStateRejectionReason(?string $stateRejectionReason): void
    {
        $this->stateRejectionReason = $stateRejectionReason;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service = null): void
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

    public function getTreatments(): Collection
    {
        return $this->treatments;
    }

    public function setTreatments(Collection $treatments)
    {
        $this->treatments = $treatments;
    }
}
