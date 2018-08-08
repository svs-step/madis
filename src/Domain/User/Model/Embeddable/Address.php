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
     * @var string
     */
    private $lineOne;

    /**
     * @var string
     */
    private $lineTwo;

    /**
     * @var string
     */
    private $city;

    /**
     * @var int
     */
    private $zipCode;

    /**
     * @var string
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
     * @return int|null
     */
    public function getZipCode(): ?int
    {
        return $this->zipCode;
    }

    /**
     * @param int|null $zipCode
     */
    public function setZipCode(?int $zipCode): void
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
