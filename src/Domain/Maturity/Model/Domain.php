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

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Domain
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
     * @var string|null
     */
    private $color;

    /**
     * @var int|null
     */
    private $position;

    /**
     * @var iterable
     */
    private $questions;

    /**
     * @var iterable
     */
    private $maturity;

    /**
     * Domain constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id        = Uuid::uuid4();
        $this->questions = [];
        $this->maturity  = [];
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

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): void
    {
        $this->color = $color;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    public function addQuestion(Question $question): void
    {
        $this->questions[] = $question;
        $question->setDomain($this);
    }

    public function removeQuestion(Question $question): void
    {
        $key = \array_search($question, $this->questions, true);

        if (false === $key) {
            return;
        }

        unset($this->questions[$key]);
    }

    public function getQuestions(): iterable
    {
        return $this->questions;
    }

    public function addMaturity(Maturity $maturity): void
    {
        $this->maturity[] = $maturity;
        $maturity->setDomain($this);
    }

    public function removeMaturity(Maturity $maturity): void
    {
        $key = \array_search($maturity, $this->maturity, true);

        if (false === $key) {
            return;
        }

        unset($this->maturity[$key]);
    }

    public function getMaturity(): iterable
    {
        return $this->maturity;
    }
}
