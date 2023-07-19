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

use App\Domain\Registry\Dictionary\MesurementStatusDictionary;
use App\Domain\Registry\Model\Mesurement;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class AnswerSurvey
{
    /**
     * @var UuidInterface
     *
     * @Serializer\Exclude
     */
    private $id;

    private ?Survey $survey;

    private ?Answer $answer;

    /**
     * @var Mesurement[]|iterable
     *
     * @Serializer\Exclude
     */
    private $mesurements;

        public function __construct()
    {
        $this->id             = Uuid::uuid4();
        $this->mesurements = [];
    }

    public function deserialize(): void
    {
        $this->id = Uuid::uuid4();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getAnswer(): Answer
    {
        return $this->answer;
    }

    public function setAnswer(?Answer $answer): void
    {
        $this->answer = $answer;
    }

    public function getSurvey(): Survey
    {
        return $this->survey;
    }

    public function setSurvey(?Survey $survey): void
    {
        $this->survey = $survey;
    }

    public function getMesurements(): iterable
    {
        return $this->mesurements;
    }

    public function getNonAppliedMesurements()
    {
        return array_filter(\iterable_to_array($this->mesurements),
            function (Mesurement $action) {
                return MesurementStatusDictionary::STATUS_NOT_APPLIED === $action->getStatus();
            });
    }

    public function setMesurements(iterable $mesurements): void
    {
        $this->mesurements = $mesurements;
    }
}
