<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
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

use App\Domain\Reporting\Model\LoggableSubject;
use App\Domain\User\Model\Embeddable\Contact;
use JsonSerializable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ComiteIlContact implements LoggableSubject, JsonSerializable
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var Contact|null
     */
    private $contact;

    /**
     * @var Collectivity
     */
    private $collectivity;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): void
    {
        $this->contact = $contact;
    }

    public function getCollectivity(): Collectivity
    {
        return $this->collectivity;
    }

    public function setCollectivity(Collectivity $collectivity): void
    {
        $this->collectivity = $collectivity;
    }

    public function jsonSerialize()
    {
        $contact = $this->getContact();

        return [
            'civilite' => $contact->getCivility(),
            'prénom'   => $contact->getFirstName(),
            'nom'      => $contact->getLastName(),
            'fonction' => $contact->getJob(),
            'email'    => $contact->getMail(),
            'tel'      => $contact->getPhoneNumber(),
        ];
    }

    public function __toString(): string
    {
        $contact = $this->getContact();

        return !\is_null($contact) ? $contact->getLastName() . ' ' . $contact->getFirstName() : '';
    }
}
