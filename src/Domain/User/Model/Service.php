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

use App\Application\Traits\Model\CollectivityTrait;
use App\Application\Traits\Model\HistoryTrait;
use App\Domain\Registry\Model\Contractor;
use App\Domain\Registry\Model\Request;
use App\Domain\Registry\Model\Treatment;
use App\Domain\Registry\Model\Violation;
use App\Domain\Reporting\Model\LoggableSubject;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Service implements LoggableSubject
{
    use CollectivityTrait;
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
     * @var User[]
     */
    private $users;

    /**
     * @var Collectivity
     */
    private $collectivity;

    /**
     * @var Treatment[]
     */
    private $treatments;

    /**
     * @var Contractor[]
     */
    private $contractors;

    /**
     * @var Request[]
     */
    private $requests;

    /**
     * @var Violation[]
     */
    private $violations;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
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

        if (\mb_strlen($this->getName()) > 50) {
            return \mb_substr($this->getName(), 0, 50) . '...';
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

    public function getUsers()
    {
        return $this->users;
    }

    public function setUsers($users): void
    {
        $this->users = $users;
    }

    public function getCollectivity(): Collectivity
    {
        return $this->collectivity;
    }

    public function setCollectivity(Collectivity $collectivity): void
    {
        $this->collectivity = $collectivity;
    }

    public function getTreatments()
    {
        return $this->treatments;
    }

    public function setTreatments($treatments): void
    {
        $this->treatments = $treatments;
    }

    public function getContractors()
    {
        return $this->contractors;
    }

    public function setContractors($contractors): void
    {
        $this->contractors = $contractors;
    }

    public function getRequests()
    {
        return $this->requests;
    }

    public function setRequests($requests): void
    {
        $this->requests = $requests;
    }

    public function getViolations()
    {
        return $this->violations;
    }

    public function setViolations($violations): void
    {
        $this->violations = $violations;
    }
}
