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

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 */
class NotificationUser
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     *
     * @var UuidInterface
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $mail;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Notification\Model\Notification")
     * @ORM\JoinColumn(name="notification_id", referencedColumnName="id")
     */
    private Notification $notification;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\User\Model\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private UserInterface $user;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private ?string $token;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $active;

    /**
     * Has the email been sent for this notification and this user.
     *
     * @ORM\Column(type="boolean")
     */
    private ?bool $sent;

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

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(?string $mail): void
    {
        $this->mail = $mail;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): void
    {
        $this->active = $active;
    }

    public function getNotification(): Notification
    {
        return $this->notification;
    }

    public function setNotification(Notification $notification): void
    {
        $this->notification = $notification;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }

    public function getSent(): ?bool
    {
        return $this->sent;
    }

    public function setSent(?bool $sent): void
    {
        $this->sent = $sent;
    }
}
