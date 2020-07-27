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
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Action de protection / Plan d'action.
 */
class Mesurement
{
    use CollectivityTrait;
    use CreatorTrait;
    use HistoryTrait;

    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * FR: Nom.
     *
     * @var string|null
     */
    private $name;

    /**
     * FR: Type.
     *
     * @var string|null
     */
    private $type;

    /**
     * FR: Description.
     *
     * @var string|null
     */
    private $description;

    /**
     * FR: Cout.
     *
     * @var string|null
     */
    private $cost;

    /**
     * FR: Charge.
     *
     * @var string|null
     */
    private $charge;

    /**
     * FR: Statut.
     *
     * @var string|null
     */
    private $status;

    /**
     * FR: Date de planification.
     *
     * @var \DateTime|null
     */
    private $planificationDate;

    /**
     * @var string|null
     */
    private $comment;

    /**
     * @var iterable
     */
    private $proofs;

    /**
     * @var Mesurement|null
     */
    private $clonedFrom;

    /**
     * @var string|null
     */
    private $priority;

    /**
     * @var string|null
     */
    private $manager;

    /**
     * @var iterable
     */
    private $conformiteOrganisation;

    /**
     * @var iterable
     */
    private $conformiteTraitementReponses;

    /**
     * Mesurement constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id                           = Uuid::uuid4();
        $this->proofs                       = [];
        $this->conformiteTraitementReponses = [];
        $this->conformiteOrganisation       = [];
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getCost(): ?string
    {
        return $this->cost;
    }

    public function setCost(?string $cost): void
    {
        $this->cost = $cost;
    }

    public function getCharge(): ?string
    {
        return $this->charge;
    }

    public function setCharge(?string $charge): void
    {
        $this->charge = $charge;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function getPlanificationDate(): ?\DateTime
    {
        return $this->planificationDate;
    }

    public function setPlanificationDate(?\DateTime $planificationDate): void
    {
        $this->planificationDate = $planificationDate;
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

    public function getClonedFrom(): ?Mesurement
    {
        return $this->clonedFrom;
    }

    public function setClonedFrom(?Mesurement $clonedFrom): void
    {
        $this->clonedFrom = $clonedFrom;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(?string $priority): void
    {
        $this->priority = $priority;
    }

    public function getManager(): ?string
    {
        return $this->manager;
    }

    public function setManager(?string $manager): void
    {
        $this->manager = $manager;
    }

    public function getConformiteOrganisation()
    {
        return $this->conformiteOrganisation;
    }

    public function getConformiteTraitementReponses()
    {
        return $this->conformiteTraitementReponses;
    }
}
