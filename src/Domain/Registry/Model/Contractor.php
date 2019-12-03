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
use App\Domain\Registry\Model\Embeddable\Address;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Contractor
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
     * @var bool
     */
    private $contractualClausesVerified;

    /**
     * @var bool
     */
    private $conform;

    /**
     * @var string|null
     */
    private $otherInformations;

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
     * Contractor constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id                         = Uuid::uuid4();
        $this->contractualClausesVerified = false;
        $this->conform                    = false;
        $this->treatments                 = [];
        $this->proofs                     = [];
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

    public function isConform(): bool
    {
        return $this->conform;
    }

    public function setConform(bool $conform): void
    {
        $this->conform = $conform;
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
}
