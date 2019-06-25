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

namespace App\Domain\Registry\Model\Embeddable;

class Address
{
    /**
     * @var string|null
     */
    private $lineOne;

    /**
     * @var string|null
     */
    private $lineTwo;

    /**
     * @var string|null
     */
    private $city;

    /**
     * @var string|null
     */
    private $zipCode;

    /**
     * @var string|null
     */
    private $mail;

    /**
     * @var string|null
     */
    private $phoneNumber;

    /**
     * @return string|null
     */
    public function getLineOne(): ?string
    {
        return $this->lineOne;
    }

    /**
     * @param string|null $lineOne
     */
    public function setLineOne(?string $lineOne): void
    {
        $this->lineOne = $lineOne;
    }

    /**
     * @return string|null
     */
    public function getLineTwo(): ?string
    {
        return $this->lineTwo;
    }

    /**
     * @param string|null $lineTwo
     */
    public function setLineTwo(?string $lineTwo): void
    {
        $this->lineTwo = $lineTwo;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     */
    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string|null
     */
    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    /**
     * @param string|null $zipCode
     */
    public function setZipCode(?string $zipCode): void
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @return string|null
     */
    public function getMail(): ?string
    {
        return $this->mail;
    }

    /**
     * @param string|null $mail
     */
    public function setMail(?string $mail): void
    {
        $this->mail = $mail;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string|null $phoneNumber
     */
    public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }
}
