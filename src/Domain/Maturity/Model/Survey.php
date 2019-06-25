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
use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Survey
{
    use CollectivityTrait;
    use CreatorTrait;
    use HistoryTrait;

    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var iterable
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
        $this->answers  = new ArrayCollection();
        $this->maturity = new ArrayCollection();
        $this->score    = 0;
    }

    public function __toString()
    {
        return "Indice du {$this->createdAt->format('d/m/Y')}";
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @param Answer $answer
     */
    public function addAnswer(Answer $answer): void
    {
        $this->answers->add($answer);
        $answer->setSurvey($this);
    }

    /**
     * @param Answer $answer
     */
    public function removeAnswer(Answer $answer): void
    {
        $this->answers->removeElement($answer);
    }

    /**
     * @return iterable
     */
    public function getAnswers(): iterable
    {
        return $this->answers;
    }

    /**
     * @param Maturity $maturity
     */
    public function addMaturity(Maturity $maturity): void
    {
        $this->maturity->add($maturity);
        $maturity->setSurvey($this);
    }

    /**
     * @param Maturity $maturity
     */
    public function removeMaturity(Maturity $maturity): void
    {
        $this->maturity->removeElement($maturity);
    }

    /**
     * @return iterable
     */
    public function getMaturity(): iterable
    {
        return $this->maturity;
    }

    /**
     * @param array $maturityList
     */
    public function setMaturity(array $maturityList): void
    {
        foreach ($maturityList as $maturity) {
            $this->maturity->add($maturity);
            $maturity->setSurvey($this);
        }
    }

    /**
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * @param int $score
     */
    public function setScore(int $score): void
    {
        $this->score = $score;
    }
}
