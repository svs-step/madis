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

use App\Application\Traits\Model\CollectivityTrait;
use App\Application\Traits\Model\CreatorTrait;
use App\Application\Traits\Model\HistoryTrait;
use App\Domain\Reporting\Model\LoggableSubject;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Survey implements LoggableSubject
{
    use CollectivityTrait;
    use CreatorTrait;
    use HistoryTrait;

    /**
     * @var UuidInterface
     */
    private $id;

    private Referentiel $referentiel;

    /**
     * @var iterable|null
     */
    private $answers;

    /**
     * @var iterable
     */
    private $maturity;

    /**
     * @var int
     */
    private $score;

    /**
     * Survey constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id       = Uuid::uuid4();
        $this->answers  = [];
        $this->maturity = [];
        $this->score    = 0;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return "Indice du {$this->createdAt->format('d/m/Y')}";
    }

    public function addAnswer(Answer $answer): void
    {
        $this->answers[] = $answer;
        $answer->addSurvey($this);
    }

    public function removeAnswer(Answer $answer): void
    {
        $key = \array_search($answer, $this->answers, true);

        if (false === $key) {
            return;
        }

        unset($this->answers[$key]);
    }

    public function getAnswers(): ?iterable
    {
        return $this->answers;
    }

    public function addMaturity(Maturity $maturity): void
    {
        $this->maturity[] = $maturity;
        $maturity->setSurvey($this);
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

    public function setMaturity(array $maturityList): void
    {
        foreach ($maturityList as $maturity) {
            $this->maturity[] = $maturity;
            $maturity->setSurvey($this);
        }
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function setScore(int $score): void
    {
        $this->score = $score;
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
