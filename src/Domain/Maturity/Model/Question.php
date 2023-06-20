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
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @Serializer\ExclusionPolicy("none")
 */
class Question
{
    /**
     * @var UuidInterface
     *
     * @Serializer\Exclude
     */
    private $id;

    private string $name;

    private int $position;

    private int $weight;

    private bool $option;

    private ?string $optionReason;

    /**
     * @var Domain|null
     *
     * @Serializer\Exclude
     */
    private $domain;

    /**
     * @var Answer[]|array
     *
     * @Serializer\Type("array<App\Domain\Maturity\Model\Answer>")
     */
    public $answers;

    /**
     * Question constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id           = Uuid::uuid4();
        $this->answers      = new ArrayCollection();
        $this->optionReason = null;
    }

    public function __clone()
    {
        $this->id = null;

        $answers = [];
        foreach ($this->answers as $answer) {
            $answers[] = clone $answer;
        }
        $this->answers = $answers;
    }

    public function deserialize(): void
    {
        $this->id = Uuid::uuid4();
        if (isset($this->answers)) {
            foreach ($this->answers as $answer) {
                $answer->deserialize();
                $answer->setQuestion($this);
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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(?int $weight): void
    {
        $this->weight = $weight;
    }

    public function getOption(): ?bool
    {
        return $this->option;
    }

    public function setOption(?bool $option): void
    {
        $this->option = $option;
    }

    public function getOptionReason(): ?string
    {
        return $this->optionReason;
    }

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

    public function addAnswer(Answer $answer): void
    {
        $this->answers[] = $answer;
        $answer->setQuestion($this);
    }
}
