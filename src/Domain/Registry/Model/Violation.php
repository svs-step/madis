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
use App\Application\Traits\Model\SoftDeletableTrait;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Violation
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
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if (\is_null($this->getDate())) {
            return '';
        }

        return "Violation du {$this->getDate()->format('d/m/Y')}";
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return \DateTime|null
     */
    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime|null $date
     */
    public function setDate(?\DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return bool
     */
    public function isInProgress(): bool
    {
        return $this->inProgress;
    }

    /**
     * @param bool $inProgress
     */
    public function setInProgress(bool $inProgress): void
    {
        $this->inProgress = $inProgress;
    }

    /**
     * @return string|null
     */
    public function getViolationNature(): ?string
    {
        return $this->violationNature;
    }

    /**
     * @param string|null $violationNature
     */
    public function setViolationNature(?string $violationNature): void
    {
        $this->violationNature = $violationNature;
    }

    /**
     * @return iterable
     */
    public function getOrigins(): iterable
    {
        return $this->origins;
    }

    /**
     * @param iterable $origins
     */
    public function setOrigins(iterable $origins): void
    {
        $this->origins = $origins;
    }

    /**
     * @return string|null
     */
    public function getCause(): ?string
    {
        return $this->cause;
    }

    /**
     * @param string|null $cause
     */
    public function setCause(?string $cause): void
    {
        $this->cause = $cause;
    }

    /**
     * @return iterable
     */
    public function getConcernedDataNature(): iterable
    {
        return $this->concernedDataNature;
    }

    /**
     * @param iterable $concernedDataNature
     */
    public function setConcernedDataNature(iterable $concernedDataNature): void
    {
        $this->concernedDataNature = $concernedDataNature;
    }

    /**
     * @return iterable
     */
    public function getConcernedPeopleCategories(): iterable
    {
        return $this->concernedPeopleCategories;
    }

    /**
     * @param iterable $concernedPeopleCategories
     */
    public function setConcernedPeopleCategories(iterable $concernedPeopleCategories): void
    {
        $this->concernedPeopleCategories = $concernedPeopleCategories;
    }

    /**
     * @return int|null
     */
    public function getNbAffectedRows(): ?int
    {
        return $this->nbAffectedRows;
    }

    /**
     * @param int|null $nbAffectedRows
     */
    public function setNbAffectedRows(?int $nbAffectedRows): void
    {
        $this->nbAffectedRows = $nbAffectedRows;
    }

    /**
     * @return int|null
     */
    public function getNbAffectedPersons(): ?int
    {
        return $this->nbAffectedPersons;
    }

    /**
     * @param int|null $nbAffectedPersons
     */
    public function setNbAffectedPersons(?int $nbAffectedPersons): void
    {
        $this->nbAffectedPersons = $nbAffectedPersons;
    }

    /**
     * @return iterable
     */
    public function getPotentialImpactsNature(): iterable
    {
        return $this->potentialImpactsNature;
    }

    /**
     * @param iterable $potentialImpactsNature
     */
    public function setPotentialImpactsNature(iterable $potentialImpactsNature): void
    {
        $this->potentialImpactsNature = $potentialImpactsNature;
    }

    /**
     * @return string|null
     */
    public function getGravity(): ?string
    {
        return $this->gravity;
    }

    /**
     * @param string|null $gravity
     */
    public function setGravity(?string $gravity): void
    {
        $this->gravity = $gravity;
    }

    /**
     * @return string|null
     */
    public function getCommunication(): ?string
    {
        return $this->communication;
    }

    /**
     * @param string|null $communication
     */
    public function setCommunication(?string $communication): void
    {
        $this->communication = $communication;
    }

    /**
     * @return string|null
     */
    public function getCommunicationPrecision(): ?string
    {
        return $this->communicationPrecision;
    }

    /**
     * @param string|null $communicationPrecision
     */
    public function setCommunicationPrecision(?string $communicationPrecision): void
    {
        $this->communicationPrecision = $communicationPrecision;
    }

    /**
     * @return string|null
     */
    public function getAppliedMeasuresAfterViolation(): ?string
    {
        return $this->appliedMeasuresAfterViolation;
    }

    /**
     * @param string|null $appliedMeasuresAfterViolation
     */
    public function setAppliedMeasuresAfterViolation(?string $appliedMeasuresAfterViolation): void
    {
        $this->appliedMeasuresAfterViolation = $appliedMeasuresAfterViolation;
    }

    /**
     * @return string|null
     */
    public function getNotification(): ?string
    {
        return $this->notification;
    }

    /**
     * @param string|null $notification
     */
    public function setNotification(?string $notification): void
    {
        $this->notification = $notification;
    }

    /**
     * @return string|null
     */
    public function getNotificationDetails(): ?string
    {
        return $this->notificationDetails;
    }

    /**
     * @param string|null $notificationDetails
     */
    public function setNotificationDetails(?string $notificationDetails): void
    {
        $this->notificationDetails = $notificationDetails;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     */
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return iterable
     */
    public function getProofs(): iterable
    {
        return $this->proofs;
    }
}
