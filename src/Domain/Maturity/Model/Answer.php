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

class Answer
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
     * @var string|null
     */
    private $recommendation;

    /**
     * @var string|null
     */
    private $response;

    /**
     * @var Question|null
     */
    private $question;

    /**
     * @var Survey[]|iterable
     */
    private $surveys;

    /**
     * Answer constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->response = '';
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getResponse(): ?int
    {
        return $this->response;
    }

    public function setResponse(?int $response): void
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

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getRecommendation(): ?string
    {
        return $this->recommendation;
    }

    /**
     * @param string|null $recommendation
     */
    public function setRecommendation(?string $recommendation): void
    {
        $this->recommendation = $recommendation;
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
}
