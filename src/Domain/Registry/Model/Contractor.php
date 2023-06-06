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
use App\Domain\Registry\Model\Embeddable\Address;
use App\Domain\Reporting\Model\LoggableSubject;
use App\Domain\User\Model\Embeddable\Contact;
use App\Domain\User\Model\Service;
use App\Domain\User\Model\User;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Contractor implements LoggableSubject, CollectivityRelated
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
     * @var string|null
     */
    private $referent;

    /**
     * Clauses contractuelles vérifiées.
     *
     * @var bool
     */
    private $contractualClausesVerified;

    /**
     * @var string|null
     */
    private $otherInformations;

    /**
     * @var string|null
     */
    private $updatedBy;

    /**
     * @var Address|null
     */
    private $address;

    /**
     * @var iterable
     */
    private $treatments;

    /**
     * @var iterable
     */
    private $proofs;

    /**
     * @var Contractor|null
     */
    private $clonedFrom;

    /**
     * Adopte les éléments de sécurité.
     *
     * @var bool
     */
    private $adoptedSecurityFeatures;

    /**
     * Registre des traitements.
     *
     * @var bool
     */
    private $maintainsTreatmentRegister;

    /**
     * Données hors UE.
     *
     * @var bool
     */
    private $sendingDataOutsideEu;

    /**
     * @var Contact|null
     */
    private $dpo;

    /**
     * @var Contact|null
     */
    private $legalManager;

    /**
     * @var bool
     */
    private $hasDpo;

    /**
     * @var Service|null
     */
    private $service;

    private Collection $mesurements;

    /**
     * Contractor constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id                         = Uuid::uuid4();
        $this->contractualClausesVerified = false;
        $this->adoptedSecurityFeatures    = false;
        $this->maintainsTreatmentRegister = false;
        $this->sendingDataOutsideEu       = false;
        $this->hasDpo                     = false;
        $this->treatments                 = [];
        $this->proofs                     = [];
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

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?string $updatedBy): void
    {
        $this->updatedBy = $updatedBy;
    }

    public function getReferent(): ?string
    {
        return $this->referent;
    }

    public function setReferent(?string $referent): void
    {
        $this->referent = $referent;
    }

    public function isContractualClausesVerified(): bool
    {
        return $this->contractualClausesVerified;
    }

    public function setContractualClausesVerified(bool $contractualClausesVerified): void
    {
        $this->contractualClausesVerified = $contractualClausesVerified;
    }

    public function getOtherInformations(): ?string
    {
        return $this->otherInformations;
    }

    public function setOtherInformations(?string $otherInformations): void
    {
        $this->otherInformations = $otherInformations;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): void
    {
        $this->address = $address;
    }

    public function addTreatment(Treatment $treatment): void
    {
        $this->treatments[] = $treatment;
    }

    public function removeTreatment(Treatment $treatment): void
    {
        $key = \array_search($treatment, $this->treatments, true);

        if (false === $key) {
            return;
        }

        unset($this->treatments[$key]);
    }

    public function getTreatments(): iterable
    {
        return $this->treatments;
    }

    public function getProofs(): iterable
    {
        return $this->proofs;
    }

    public function getClonedFrom(): ?Contractor
    {
        return $this->clonedFrom;
    }

    public function setClonedFrom(?Contractor $clonedFrom): void
    {
        $this->clonedFrom = $clonedFrom;
    }

    public function isAdoptedSecurityFeatures(): bool
    {
        return $this->adoptedSecurityFeatures;
    }

    public function setAdoptedSecurityFeatures(bool $adoptedSecurityFeatures): void
    {
        $this->adoptedSecurityFeatures = $adoptedSecurityFeatures;
    }

    public function isMaintainsTreatmentRegister(): bool
    {
        return $this->maintainsTreatmentRegister;
    }

    public function setMaintainsTreatmentRegister(bool $maintainsTreatmentRegister): void
    {
        $this->maintainsTreatmentRegister = $maintainsTreatmentRegister;
    }

    public function isSendingDataOutsideEu(): bool
    {
        return $this->sendingDataOutsideEu;
    }

    public function setSendingDataOutsideEu(bool $sendingDataOutsideEu): void
    {
        $this->sendingDataOutsideEu = $sendingDataOutsideEu;
    }

    public function getDpo(): ?Contact
    {
        return $this->dpo;
    }

    public function setDpo(?Contact $dpo): void
    {
        $this->dpo = $dpo;
    }

    public function getLegalManager(): ?Contact
    {
        return $this->legalManager;
    }

    public function setLegalManager(?Contact $legalManager): void
    {
        $this->legalManager = $legalManager;
    }

    public function isHasDpo(): bool
    {
        return $this->hasDpo;
    }

    public function setHasDpo(bool $hasDpo): void
    {
        $this->hasDpo = $hasDpo;
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

    public function getMesurements(): ?Collection
    {
        return $this->mesurements;
    }

    public function setMesurement(?Collection $mesurements): void
    {
        $this->mesurements = $mesurements;
    }
}
