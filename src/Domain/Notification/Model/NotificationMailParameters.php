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
    private $is_notified = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\User\Model\User")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private ?User $user;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string|null
     */
    private $frequency;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int|null
     */
    private ?int $interval_hours = 1;

    /**
     * @ORM\Column(type="text",  nullable=true)
     *
     * @var string|null
     */
    private $start_week;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string|null
     */
    private $start_day;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     */
    private ?int $start_hour;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_treatment = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_subcontract = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_request = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_violation = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_proof = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_protectAction = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_maturity = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_treatmenConformity = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_organizationConformity = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_AIPD = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_document = false;

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

    public function setIsNotified($is_notified): void
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

    public function getFrequency(): ?string
    {
        return (string) $this->frequency;
    }

    public function setFrequency(?string $frequency): void
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

    public function getStartWeek(): ?string
    {
        return (string) $this->start_week;
    }

    public function setStartWeek(?string $start_week): void
    {
        $this->start_week = $start_week;
    }

    public function getStartDay(): ?string
    {
        return (string) $this->start_day;
    }

    public function setStartDay(?string $start_day): void
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

    public function setIsTreatment($is_treatment): void
    {
        $this->is_treatment = $is_treatment;
    }

    public function getIsSubcontract(): ?bool
    {
        return $this->is_subcontract;
    }

    public function setIsSubcontract($is_subcontract): void
    {
        $this->is_subcontract = $is_subcontract;
    }

    public function getIsRequest(): ?bool
    {
        return $this->is_request;
    }

    public function setIsRequest($is_request): void
    {
        $this->is_request = $is_request;
    }

    public function getIsViolation(): ?bool
    {
        return $this->is_violation;
    }

    public function setIsViolation($is_violation): void
    {
        $this->is_violation = $is_violation;
    }

    public function getIsProof(): ?bool
    {
        return $this->is_proof;
    }

    public function setIsProof($is_proof): void
    {
        $this->is_proof = $is_proof;
    }

    public function getIsProtectAction(): ?bool
    {
        return $this->is_protectAction;
    }

    public function setIsProtectAction($is_protectAction): void
    {
        $this->is_protectAction = $is_protectAction;
    }

    public function getIsMaturity(): ?bool
    {
        return $this->is_maturity;
    }

    public function setIsMaturity($is_maturity): void
    {
        $this->is_maturity = $is_maturity;
    }

    public function getIsTreatmenConformity(): ?bool
    {
        return $this->is_treatmenConformity;
    }

    public function setIsTreatmenConformity($is_treatmenConformity): void
    {
        $this->is_treatmenConformity = $is_treatmenConformity;
    }

    public function getIsOrganizationConformity(): ?bool
    {
        return $this->is_organizationConformity;
    }

    public function setIsOrganizationConformity($is_organizationConformity): void
    {
        $this->is_organizationConformity = $is_organizationConformity;
    }

    public function getIsAIPD(): ?bool
    {
        return $this->is_AIPD;
    }

    public function setIsAIPD($is_AIPD): void
    {
        $this->is_AIPD = $is_AIPD;
    }

    public function getIsDocument(): ?bool
    {
        return $this->is_document;
    }

    public function setIsDocument($is_document): void
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

