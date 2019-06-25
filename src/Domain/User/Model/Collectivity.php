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

namespace App\Domain\User\Model;

use App\Application\Traits\Model\HistoryTrait;
use App\Domain\User\Model\Embeddable\Address;
use App\Domain\User\Model\Embeddable\Contact;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Collectivity
{
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
    private $shortName;

    /**
     * @var string|null
     */
    private $type;

    /**
     * @var int|null
     */
    private $siren;

    /**
     * @var bool
     */
    private $active;

    /**
     * @var string|null
     */
    private $website;

    /**
     * @var Address|null
     */
    private $address;

    /**
     * @var Contact|null
     */
    private $legalManager;

    /**
     * @var Contact|null
     */
    private $referent;

    /**
     * @var bool
     */
    private $differentDpo;
    /**
     * @var Contact|null
     */
    private $dpo;

    /**
     * @var bool
     */
    private $differentItManager;

    /**
     * @var Contact|null
     */
    private $itManager;

    /**
     * @var Collection
     */
    private $users;

    /**
     * Collectivity constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id                 = Uuid::uuid4();
        $this->users              = new ArrayCollection();
        $this->active             = true;
        $this->differentDpo       = false;
        $this->differentItManager = false;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if (\is_null($this->getName())) {
            return '';
        }

        if (\strlen($this->getName()) > 50) {
            return \substr($this->getName(), 0, 50) . '...';
        }

        return $this->getName();
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    /**
     * @param string $shortName
     */
    public function setShortName(string $shortName): void
    {
        $this->shortName = $shortName;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return int|null
     */
    public function getSiren(): ?int
    {
        return $this->siren;
    }

    /**
     * @param int $siren
     */
    public function setSiren(int $siren): void
    {
        $this->siren = $siren;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @return string|null
     */
    public function getWebsite(): ?string
    {
        return $this->website;
    }

    /**
     * @param string $website
     */
    public function setWebsite(string $website): void
    {
        $this->website = $website;
    }

    /**
     * @return Address|null
     */
    public function getAddress(): ?Address
    {
        return $this->address;
    }

    /**
     * @param Address $address
     */
    public function setAddress(Address $address): void
    {
        $this->address = $address;
    }

    /**
     * @return Contact|null
     */
    public function getLegalManager(): ?Contact
    {
        return $this->legalManager;
    }

    /**
     * @param Contact $legalManager
     */
    public function setLegalManager(Contact $legalManager): void
    {
        $this->legalManager = $legalManager;
    }

    /**
     * @return Contact|null
     */
    public function getReferent(): ?Contact
    {
        return $this->referent;
    }

    /**
     * @param Contact $referent
     */
    public function setReferent(Contact $referent): void
    {
        $this->referent = $referent;
    }

    /**
     * @return bool
     */
    public function isDifferentDpo(): bool
    {
        return $this->differentDpo;
    }

    /**
     * @param bool $differentDpo
     */
    public function setDifferentDpo(bool $differentDpo): void
    {
        $this->differentDpo = $differentDpo;
    }

    /**
     * @return Contact|null
     */
    public function getDpo(): ?Contact
    {
        return $this->dpo;
    }

    /**
     * @param Contact $dpo
     */
    public function setDpo(Contact $dpo): void
    {
        $this->dpo = $dpo;
    }

    /**
     * @return bool
     */
    public function isDifferentItManager(): bool
    {
        return $this->differentItManager;
    }

    /**
     * @param bool $differentItManager
     */
    public function setDifferentItManager(bool $differentItManager): void
    {
        $this->differentItManager = $differentItManager;
    }

    /**
     * @return Contact|null
     */
    public function getItManager(): ?Contact
    {
        return $this->itManager;
    }

    /**
     * @param Contact $itManager
     */
    public function setItManager(Contact $itManager): void
    {
        $this->itManager = $itManager;
    }

    /**
     * @return Collection
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @param Collection $users
     */
    public function setUsers(Collection $users): void
    {
        $this->users = $users;
    }
}
