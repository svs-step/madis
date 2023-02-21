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
use Doctrine\Common\Collections\ArrayCollection;
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

    /**
     * @var int
     */
    private $score;

    /**
     * @var Referentiel
     */
    private $referentiel;

    /**
     * @var iterable
     */
    private $answers;

    /**
     * Survey constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id    = Uuid::uuid4();
        $this->score = 0;
        $this->answers = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return "Indice du {$this->createdAt->format('d/m/Y')}";
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

    /**
     * @return ReferentielAnswer[]|iterable
     */
    public function getAnswers(): iterable
    {
        return $this->answers;
    }

    /**
     * @param ReferentielAnswer[]|iterable $answers
     */
    public function setAnswers(iterable $answers): void
    {
        $this->answers = $answers;
    }

    public function getSections(): iterable
    {
        return $this->referentiel ? $this->referentiel->getReferentielSections() : [];
    }
}
