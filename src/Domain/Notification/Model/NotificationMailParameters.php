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

namespace App\Domain\Notification\Model;

use App\Domain\User\Model\User;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 */
class NotificationMailParameters
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     *
     * @var UuidInterface
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?string $is_notified;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\User\Model\User")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private ?User $user;

    /**
     * @ORM\Column(type="json_array")
     *
     * @var array|object|null
     */
    private $frequency;

    /**
     * @ORM\Column(type="integer")
     */
    private ?string $interval_hours;

    /**
     * @ORM\Column(type="json_array")
     *
     * @var array|object|null
     */
    private $start_week;

    /**
     * @ORM\Column(type="json_array")
     *
     * @var array|object|null
     */
    private $start_day;

    /**
     * @ORM\Column(type="integer")
     *
     */
    private $start_hour;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?string $is_treatment;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?string $is_subcontract;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?string $is_request;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?string $is_violation;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?string $is_proof;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?string $is_protectAction;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?string $is_maturity;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?string $is_treatmenConformity;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?string $is_organizationConformity;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?string $is_AIPD;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?string $is_document;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $last_notif_send;

    /**
     * Category constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getIsNotified(): ?bool
    {
        return $this->is_notified;
    }

    public function setIsNotified(?bool $is_notified): void
    {
        $this->is_notified = $is_notified;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getFrequency(): ?object
    {
        return (object) $this->frequency;
    }

    public function setFrequency(?object $frequency): void
    {
        $this->frequency = $frequency;
    }

    public function getIntervalHours(): ?int
    {
        return $this->interval_hours;
    }

    public function setIntervalHours(?int $interval_hours): void
    {
        $this->interval_hours = $interval_hours;
    }

    public function getStartWeek(): ?object
    {
        return (object) $this->start_week;
    }

    public function setStartWeek(?object $start_week): void
    {
        $this->start_week = $start_week;
    }

    public function getStartDay(): ?object
    {
        return (object) $this->start_day;
    }

    public function setStartDay(?object $start_day): void
    {
        $this->start_day = $start_day;
    }

    public function getStartHours(): ?int
    {
        return $this->start_hour;
    }

    public function setStartHours(?int $start_hour): void
    {
        $this->start_hour = $start_hour;
    }

    public function getIsTreatment(): ?bool
    {
        return $this->is_treatment;
    }

    public function setIsTreatment(?bool $is_treatment): void
    {
        $this->is_treatment = $is_treatment;
    }

    public function getIsSubcontract(): ?bool
    {
        return $this->is_subcontract;
    }

    public function setIsSubcontract(?bool $is_subcontract): void
    {
        $this->is_subcontract = $is_subcontract;
    }

    public function getIsRequest(): ?bool
    {
        return $this->is_request;
    }

    public function setIsRequest(?bool $is_request): void
    {
        $this->is_request = $is_request;
    }

    public function getIsViolation(): ?bool
    {
        return $this->is_violation;
    }

    public function setIsViolation(?bool $is_violation): void
    {
        $this->is_violation = $is_violation;
    }

    public function getIsProof(): ?bool
    {
        return $this->is_proof;
    }

    public function setIsProof(?bool $is_proof): void
    {
        $this->is_proof = $is_proof;
    }

    public function getIsProtection(): ?bool
    {
        return $this->is_protectAction;
    }

    public function setIsProtection(?bool $is_protectAction): void
    {
        $this->is_protectAction = $is_protectAction;
    }

    public function getIsMaturity(): ?bool
    {
        return $this->is_maturity;
    }

    public function setIsMaturity(?bool $is_maturity): void
    {
        $this->is_maturity = $is_maturity;
    }

    public function getIsTreatmenConformity(): ?bool
    {
        return $this->is_treatmenConformity;
    }

    public function setIsTreatmenConformity(?bool $is_treatmenConformity): void
    {
        $this->is_treatmenConformity = $is_treatmenConformity;
    }

    public function getIsOrganizationConformity(): ?bool
    {
        return $this->is_organizationConformity;
    }

    public function setIsOrganizationConformity(?bool $is_organizationConformity): void
    {
        $this->is_organizationConformity = $is_organizationConformity;
    }

    public function getIsAIPD(): ?bool
    {
        return $this->is_AIPD;
    }

    public function setIsAIPD(?bool $is_AIPD): void
    {
        $this->is_AIPD = $is_AIPD;
    }

    public function getIsDocument(): ?bool
    {
        return $this->is_document;
    }

    public function setIsDocument(?bool $is_document): void
    {
        $this->is_document = $is_document;
    }

    public function getLastNotifSend(): ?\DateTime
    {
        return $this->last_notif_send;
    }

    public function setLastNotifSend(?\DateTime $last_notif_send): void
    {
        $this->last_notif_send = $last_notif_send;
    }
}

