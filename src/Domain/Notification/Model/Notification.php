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

use App\Application\Traits\Model\CreatorTrait;
use App\Application\Traits\Model\HistoryTrait;
use App\Domain\AIPD\Model\AnalyseImpact;
use App\Domain\Documentation\Model\Document;
use App\Domain\Maturity\Model\Maturity;
use App\Domain\Registry\Model\ConformiteOrganisation\Conformite;
use App\Domain\Registry\Model\ConformiteTraitement\ConformiteTraitement;
use App\Domain\Registry\Model\Contractor;
use App\Domain\Registry\Model\Mesurement;
use App\Domain\Registry\Model\Proof;
use App\Domain\Registry\Model\Request;
use App\Domain\Registry\Model\Treatment;
use App\Domain\Registry\Model\Violation;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class Notification
{
    use HistoryTrait;
    use CreatorTrait;

    public const NOTIFICATION_DPO          = 1;
    public const NOTIFICATION_COLLECTIVITY = 2;

    public const MODULES = [
        Treatment::class            => 'treatment',
        Contractor::class           => 'subcontractor',
        Request::class              => 'request',
        Violation::class            => 'violation',
        Proof::class                => 'proof',
        Mesurement::class           => 'protect_action',
        Maturity::class             => 'maturity',
        ConformiteTraitement::class => 'treatment_conformity',
        Conformite::class           => 'organization_conformity',
        AnalyseImpact::class        => 'aipd',
        Document::class             => 'document',
        User::class                 => 'user',
    ];

    /**
     * @ORM\Id()
     *
     * @ORM\Column(type="uuid")
     *
     * @var UuidInterface
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     *
     * @var string|null
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $dpo;

    /**
     * @ORM\Column(type="string")
     *
     * @var string|null
     */
    private $module;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $subject;

    /**
     * @ORM\Column(type="string")
     *
     * @var string|null
     */
    private $action;

    /**
     * @ORM\Column(type="object")
     *
     * @var object|null
     */
    private $object;

    /**
     * @var Collectivity|null
     *
     * @ORM\ManyToOne(targetEntity="App\Domain\User\Model\Collectivity")
     *
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $collectivity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\User\Model\User")
     *
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private ?User $readBy;

    /**
     * @ORM\Column(type="datetime", name="read_at", nullable="true")
     */
    private ?\DateTime $readAt = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\User\Model\User")
     *
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private ?User $createdBy;

    /**
     * @ORM\OneToMany(mappedBy="notification", targetEntity="App\Domain\Notification\Model\NotificationUser", cascade={"persist", "remove"})
     */
    private Collection|array $notificationUsers = [];

    /**
     * Category constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id        = Uuid::uuid4();
        $this->dpo       = false;
        $this->createdBy = null;
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

    public function getModule(): ?string
    {
        return $this->module;
    }

    public function setModule(?string $module): void
    {
        $this->module = $module;
    }

    public function getObject(): ?object
    {
        return $this->object;
    }

    public function setObject(?object $object): void
    {
        $this->object = $object;
    }

    public function getCollectivity(): ?Collectivity
    {
        return $this->collectivity;
    }

    public function setCollectivity(?Collectivity $collectivity): void
    {
        $this->collectivity = $collectivity;
    }

    public function getReadBy(): ?User
    {
        return $this->readBy;
    }

    public function setReadBy(?User $readBy): void
    {
        $this->readBy = $readBy;
    }

    public function getReadAt(): ?\DateTime
    {
        return $this->readAt;
    }

    public function setReadAt(?\DateTime $readAt): void
    {
        $this->readAt = $readAt;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(?string $action): void
    {
        $this->action = $action;
    }

    public function getNotificationUsers(): array|Collection
    {
        return $this->notificationUsers;
    }

    public function setNotificationUsers(array|Collection $notificationUsers): void
    {
        $this->notificationUsers = $notificationUsers;
    }

    public function getDpo(): ?bool
    {
        return $this->dpo;
    }

    public function setDpo(?bool $dpo): void
    {
        $this->dpo = $dpo;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): void
    {
        $this->subject = $subject;
    }
}
