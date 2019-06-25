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

class Delay
{
    /**
     * @var int|null
     */
    private $number;

    /**
     * @var string|null
     */
    private $period;

    /**
     * @var bool
     */
    private $otherDelay;

    /**
     * @var string|null
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
