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

namespace App\Domain\Registry\Model\Embeddable;

class Delay
{
    /**
     * @var int
     */
    private $number;

    /**
     * @var string
     */
    private $period;

    /**
     * @var bool
     */
    private $otherDelay;

    /**
     * @var string
     */
    private $comment;

    public function __construct()
    {
        $this->otherDelay = false;
    }

    /**
     * @return int|null
     */
    public function getNumber(): ?int
    {
        return $this->number;
    }

    /**
     * @param int|null $number
     */
    public function setNumber(?int $number): void
    {
        $this->number = $number;
    }

    /**
     * @return string|null
     */
    public function getPeriod(): ?string
    {
        return $this->period;
    }

    /**
     * @param string|null $period
     */
    public function setPeriod(?string $period): void
    {
        $this->period = $period;
    }

    /**
     * @return bool
     */
    public function isOtherDelay(): bool
    {
        return $this->otherDelay;
    }

    /**
     * @param bool $otherDelay
     */
    public function setOtherDelay(bool $otherDelay): void
    {
        $this->otherDelay = $otherDelay;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     */
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }
}
