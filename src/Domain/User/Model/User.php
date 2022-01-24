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

use App\Application\Interfaces\CollectivityRelated;
use App\Application\Traits\Model\SoftDeletableTrait;
use App\Domain\Reporting\Model\LoggableSubject;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements LoggableSubject, UserInterface, CollectivityRelated
{
    use SoftDeletableTrait;

    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var string|null
     */
    private $firstName;

    /**
     * @var string|null
     */
    private $lastName;

    /**
     * @var string|null
     */
    private $email;

    /**
     * @var string|null
     */
    private $password;

    /**
     * @var string|null
     */
    private $plainPassword;

    /**
     * @var string|null
     */
    private $forgetPasswordToken;

    /**
     * @var array
     */
    private $roles;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var Collectivity
     */
    private $collectivity;

    /**
     * @var Collection|Service[]
     */
    private $services;

    /**
     * @var iterable
     */
    private $collectivitesReferees;

    /**
     * @var \DateTimeImmutable|null
     */
    private $lastLogin;

    /**
     * @var bool
     */
    private $apiAuthorized;

    /**
     * @var array|null
     */
    private $favoriteDocuments;

    /**
     * User constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id                    = Uuid::uuid4();
        $this->roles                 = [];
        $this->enabled               = true;
        $this->collectivitesReferees = [];
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function __toString(): string
    {
        if (\is_null($this->getFullName())) {
            return '';
        }

        if (\mb_strlen($this->getFullName()) > 50) {
            return \mb_substr($this->getFullName(), 0, 50) . '...';
        }

        return $this->getFullName();
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getFullName(): string
    {
        return "{$this->firstName} {$this->lastName}";
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getUsername(): ?string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getForgetPasswordToken(): string
    {
        return $this->forgetPasswordToken;
    }

    public function setForgetPasswordToken(?string $forgetPasswordToken): void
    {
        $this->forgetPasswordToken = $forgetPasswordToken;
    }

    public function getSalt()
    {
        return null;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getCollectivity(): ?Collectivity
    {
        return $this->collectivity;
    }

    public function setCollectivity(Collectivity $collectivity): void
    {
        $this->collectivity = $collectivity;
    }

    public function getLastLogin(): ?\DateTimeImmutable
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeImmutable $lastLogin): void
    {
        $this->lastLogin = $lastLogin;
    }

    public function getServices(): ?Collection
    {
        return $this->services;
    }

    public function setServices($services): void
    {
        $this->services = $services;
    }

    public function getCollectivitesReferees(): iterable
    {
        return $this->collectivitesReferees;
    }

    public function setCollectivitesReferees(iterable $collectivitesReferees): void
    {
        $this->collectivitesReferees = $collectivitesReferees;
    }

    public function getApiAuthorized(): ?bool
    {
        return $this->apiAuthorized;
    }

    public function setApiAuthorized(?bool $apiAuthorized): void
    {
        $this->apiAuthorized = $apiAuthorized;
    }

    public function isInUserServices(User $user): bool
    {
        if (false == $user->getCollectivity()->getIsServicesEnabled()) {
            return true;
        }

        $result = false;

        if ($user->getServices() === $this->getServices()) {
            $result = true;
        }

        return $result;
    }
}
