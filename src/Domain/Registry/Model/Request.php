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

namespace App\Domain\Registry\Model;

use App\Application\Traits\Model\CollectivityTrait;
use App\Application\Traits\Model\CreatorTrait;
use App\Application\Traits\Model\HistoryTrait;
use App\Application\Traits\Model\SoftDeletableTrait;
use App\Domain\Registry\Model\Embeddable\RequestAnswer;
use App\Domain\Registry\Model\Embeddable\RequestApplicant;
use App\Domain\Registry\Model\Embeddable\RequestConcernedPeople;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Request
{
    use CollectivityTrait;
    use CreatorTrait;
    use HistoryTrait;
    use SoftDeletableTrait;

    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var string|null
     */
    private $object;

    /**
     * @var string|null
     */
    private $otherObject;

    /**
     * @var \DateTime|null
     */
    private $date;

    /**
     * @var string|null
     */
    private $reason;

    /**
     * @var RequestApplicant|null
     */
    private $applicant;

    /**
     * @var RequestConcernedPeople|null
     */
    private $concernedPeople;

    /**
     * @var bool
     */
    private $complete;

    /**
     * @var bool
     */
    private $legitimateApplicant;

    /**
     * @var bool
     */
    private $legitimateRequest;

    /**
     * @var RequestAnswer|null
     */
    private $answer;

    /**
     * @var iterable
     */
    private $proofs;

    /**
     * Request constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id                  = Uuid::uuid4();
        $this->date                = new \DateTime();
        $this->applicant           = new RequestApplicant();
        $this->concernedPeople     = new RequestConcernedPeople();
        $this->answer              = new RequestAnswer();
        $this->complete            = false;
        $this->legitimateApplicant = false;
        $this->legitimateRequest   = false;
        $this->proofs              = [];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if (\is_null($this->getApplicant()->getFirstName())) {
            return '';
        }

        if (\strlen($this->getApplicant()->getFullName()) > 50) {
            return \substr($this->getApplicant()->getFullName(), 0, 50) . '...';
        }

        return $this->getApplicant()->getFullName();
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getObject(): ?string
    {
        return $this->object;
    }

    /**
     * @param string|null $object
     */
    public function setObject(?string $object): void
    {
        $this->object = $object;
    }

    /**
     * @return string|null
     */
    public function getOtherObject(): ?string
    {
        return $this->otherObject;
    }

    /**
     * @param string|null $otherObject
     */
    public function setOtherObject(?string $otherObject): void
    {
        $this->otherObject = $otherObject;
    }

    /**
     * @return \DateTime|null
     */
    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime|null $date
     */
    public function setDate(?\DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return string|null
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }

    /**
     * @param string|null $reason
     */
    public function setReason(?string $reason): void
    {
        $this->reason = $reason;
    }

    /**
     * @return RequestApplicant|null
     */
    public function getApplicant(): ?RequestApplicant
    {
        return $this->applicant;
    }

    /**
     * @param RequestApplicant|null $applicant
     */
    public function setApplicant(?RequestApplicant $applicant): void
    {
        $this->applicant = $applicant;
    }

    /**
     * @return RequestConcernedPeople|null
     */
    public function getConcernedPeople(): ?RequestConcernedPeople
    {
        return $this->concernedPeople;
    }

    /**
     * @param RequestConcernedPeople|null $concernedPeople
     */
    public function setConcernedPeople(?RequestConcernedPeople $concernedPeople): void
    {
        $this->concernedPeople = $concernedPeople;
    }

    /**
     * @return bool
     */
    public function isComplete(): bool
    {
        return $this->complete;
    }

    /**
     * @param bool $complete
     */
    public function setComplete(bool $complete): void
    {
        $this->complete = $complete;
    }

    /**
     * @return bool
     */
    public function isLegitimateApplicant(): bool
    {
        return $this->legitimateApplicant;
    }

    /**
     * @param bool $legitimateApplicant
     */
    public function setLegitimateApplicant(bool $legitimateApplicant): void
    {
        $this->legitimateApplicant = $legitimateApplicant;
    }

    /**
     * @return bool
     */
    public function isLegitimateRequest(): bool
    {
        return $this->legitimateRequest;
    }

    /**
     * @param bool $legitimateRequest
     */
    public function setLegitimateRequest(bool $legitimateRequest): void
    {
        $this->legitimateRequest = $legitimateRequest;
    }

    /**
     * @return RequestAnswer|null
     */
    public function getAnswer(): ?RequestAnswer
    {
        return $this->answer;
    }

    /**
     * @param RequestAnswer|null $answer
     */
    public function setAnswer(?RequestAnswer $answer): void
    {
        $this->answer = $answer;
    }

    /**
     * @return iterable
     */
    public function getProofs(): iterable
    {
        return $this->proofs;
    }
}
