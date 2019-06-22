<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan@awkan.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\User\Model\Embeddable;

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
    private $insee;

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
    public function getInsee(): ?string
    {
        return $this->insee;
    }

    /**
     * @param string|null $insee
     */
    public function setInsee(?string $insee): void
    {
        $this->insee = $insee;
    }
}
