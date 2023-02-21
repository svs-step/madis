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

namespace App\Domain\Maturity\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Question
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var int|null
     */
    private $position;

    /**
     * @var int|null
     */
    private $weight;

    /**
     * @var bool|null
     */
    private $option;

    /**
     * @var string|null
     */
    private $optionReason;

    /**
     * @var Domain|null
     */
    private $domain;

    /**
     * @var iterable|null
     */
    private $answers;

    /**
     * Question constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id      = Uuid::uuid4();
        $this->answers = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDomain(): ?Domain
    {
        return $this->domain;
    }

    public function setDomain(?Domain $domain): void
    {
        $this->domain = $domain;
    }

    public function getAnswers(): ?iterable
    {
        return $this->answers;
    }

    /**
     * @return int|null
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @param int|null $position
     */
    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    /**
     * @return int|null
     */
    public function getWeight(): ?int
    {
        return $this->weight;
    }

    /**
     * @param int|null $weight
     */
    public function setWeight(?int $weight): void
    {
        $this->weight = $weight;
    }

    /**
     * @return bool|null
     */
    public function getOption(): ?bool
    {
        return $this->option;
    }

    /**
     * @param bool|null $option
     */
    public function setOption(?bool $option): void
    {
        $this->option = $option;
    }

    /**
     * @return string|null
     */
    public function getOptionReason(): ?string
    {
        return $this->optionReason;
    }

    /**
     * @param string|null $optionReason
     */
    public function setOptionReason(?string $optionReason): void
    {
        $this->optionReason = $optionReason;
    }

    /**
     * @param iterable|null $answers
     */
    public function setAnswers(iterable|ArrayCollection|null $answers): void
    {
        $this->answers = $answers;
    }

    /**
     * @param Answer $answer
     */
    public function addAnswer(Answer $answer): void
    {
        $this->answers[] = $answer;
        $answer->setQuestion($this);
    }
}
