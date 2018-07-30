<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan.bourlard@outlook.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
     * @var int
     */
    private $response;

    /**
     * @var Question
     */
    private $question;

    /**
     * @var Survey
     */
    private $survey;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getResponse(): ?int
    {
        return $this->response;
    }

    /**
     * @param int|null $response
     */
    public function setResponse(?int $response): void
    {
        $this->response = $response;
    }

    /**
     * @return Question|null
     */
    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    /**
     * @param Question|null $question
     */
    public function setQuestion(?Question $question): void
    {
        $this->question = $question;
    }

    /**
     * @return Survey|null
     */
    public function getSurvey(): ?Survey
    {
        return $this->survey;
    }

    /**
     * @param Survey|null $survey
     */
    public function setSurvey(?Survey $survey): void
    {
        $this->survey = $survey;
    }
}
