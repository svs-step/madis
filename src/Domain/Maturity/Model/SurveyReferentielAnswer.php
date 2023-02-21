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

class SurveyReferentielAnswer
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var Survey
     */
    private $survey;

    /**
     * @var ReferentielAnswer
     */
    private $answer;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return Survey
     */
    public function getSurvey(): Survey
    {
        return $this->survey;
    }

    /**
     * @param Survey $survey
     */
    public function setSurvey(Survey $survey): void
    {
        $this->survey = $survey;
    }

    /**
     * @return ReferentielAnswer
     */
    public function getAnswer(): ReferentielAnswer
    {
        return $this->answer;
    }

    /**
     * @param ReferentielAnswer $answer
     */
    public function setAnswer(ReferentielAnswer $answer): void
    {
        $this->answer = $answer;
    }
}
