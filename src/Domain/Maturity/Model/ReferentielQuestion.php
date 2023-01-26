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

class ReferentielQuestion
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
    private $weight;

    /**
     * @var int|null
     */
    public $questionNumber;

    /**
     * @var ReferentielSection|null
     */
    private $referentielSection;

    /**
     * @var iterable|ReferentielAnswer[]
     */
    private $referentielAnswers;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->referentielAnswers = new ArrayCollection();
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

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(?int $weight): void
    {
        $this->weight = $weight;
    }

    public function getQuestionNumber(): ?int
    {
        return $this->questionNumber;
    }

    public function setQuestionNumber(?int $questionNumber): void
    {
        $this->questionNumber = $questionNumber;
    }

    public function getReferentielSection(): ?ReferentielSection
    {
        return $this->referentielSection;
    }

    public function setReferentielSection(?ReferentielSection $referentielSection): void
    {
        $this->referentielSection = $referentielSection;
    }

    public function getReferentielAnswers(): ?iterable
    {
        return $this->referentielAnswers;
    }

    public function setReferentielAnswers(?iterable $referentielAnswers): void
    {
        $this->referentielAnswers = $referentielAnswers;
    }

    public function addReferentielAnswer(ReferentielAnswer $referentielAnswer): void
    {
        $this->referentielAnswers[] = $referentielAnswer;
    }

    public function removeReferentielAnswer(ReferentielAnswer $referentielAnswer): void
    {
        $key = \array_search($referentielAnswer, $this->referentielAnswers, true);

        if (false === $key) {
            return;
        }

        unset($this->referentielAnswers[$key]);
    }



}
