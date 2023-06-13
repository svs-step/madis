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

class Answer
{
    /**
     * @var UuidInterface
     *
     * @Serializer\Exclude
     */
    private $id;

    private string $name;

    private int $position;

    private ?string $recommendation;

    private ?string $response;

    /**
     * @var Question|null
     *
     * @Serializer\Exclude
     */
    private $question;

    /**
     * @var Survey[]|iterable
     *
     * @Serializer\Exclude
     */
    private $surveys;

    /**
     * Answer constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id             = Uuid::uuid4();
        $this->name           = '';
        $this->response       = '';
        $this->recommendation = '';
    }

    public function deserialize(): void
    {
        $this->id = Uuid::uuid4();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setResponse(?string $response): void
    {
        $this->response = $response;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): void
    {
        $this->question = $question;
    }

    public function getSurveys(): iterable
    {
        return $this->surveys;
    }

    public function setSurveys(iterable $surveys): void
    {
        $this->surveys = $surveys;
    }

    public function addSurvey(Survey $survey): void
    {
        $this->surveys[] = $survey;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getRecommendation(): ?string
    {
        return $this->recommendation;
    }

    public function setRecommendation(?string $recommendation): void
    {
        $this->recommendation = $recommendation;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }
}
