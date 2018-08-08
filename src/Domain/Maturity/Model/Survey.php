<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan@awkan.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
