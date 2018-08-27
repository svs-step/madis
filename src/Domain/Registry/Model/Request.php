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
     * @var string
     */
    private $object;

    /**
     * @var string
     */
    private $otherObject;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var string
     */
    private $reason;

    /**
     * @var RequestApplicant
     */
    private $applicant;

    /**
     * @var RequestConcernedPeople
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
     * @var RequestAnswer
     */
    private $answer;

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
    }

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
}
