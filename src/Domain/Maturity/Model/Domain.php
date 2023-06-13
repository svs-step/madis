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

use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @Serializer\ExclusionPolicy("none")
 */
class Domain
{
    /**
     * @var UuidInterface
     *
     * @Serializer\Exclude
     */
    private $id;

    private string $name;

    /**
     * @var string|null
     */
    private $description;

    private string $color;

    private int $position;

    /**
     * @var Question[]|array
     *
     * @Serializer\Type("array<App\Domain\Maturity\Model\Question>")
     */
    public $questions;

    /**
     * @var iterable
     *
     * @Serializer\Exclude
     */
    private $maturity;

    /**
     * @var Referentiel
     *
     * @Serializer\Exclude
     */
    private $referentiel;

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

    public function __clone()
    {
        $this->id  = null;
        $questions = [];
        foreach ($this->questions as $question) {
            $questions[] = clone $question;
        }
        $this->questions = $questions;
    }

    public function deserialize(): void
    {
        $this->id = Uuid::uuid4();

        foreach ($this->questions as $question) {
            if (isset($question)) {
                $question->deserialize();
                $question->setDomain($this);
            }
        }
    }

    public function __toString(): string
    {
        if (\is_null($this->getName())) {
            return '';
        }

        if (\mb_strlen($this->getName()) > 85) {
            return \mb_substr($this->getName(), 0, 85) . '...';
        }

        return $this->getName();
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

    public function addQuestion(Question $question)
    {
        $this->questions[] = $question;
        $question->setDomain($this);
    }

    public function setQuestions($questions)
    {
        $this->questions = $questions;
    }

    public function removeQuestion(Question $question)
    {
        $this->questions->removeElement($question);
    }

    public function getQuestions()
    {
        return $this->questions;
    }

    public function addMaturity(Maturity $maturity)
    {
        $this->maturity = $maturity;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getReferentiel(): Referentiel
    {
        return $this->referentiel;
    }

    public function setReferentiel(Referentiel $referentiel): void
    {
        $this->referentiel = $referentiel;
    }
}
