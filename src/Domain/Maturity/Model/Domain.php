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

use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Domain
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $color;

    /**
     * @var iterable
     */
    private $questions;

    /**
     * @var iterable
     */
    private $maturity;

    public function __construct()
    {
        $this->id        = Uuid::uuid4();
        $this->questions = new ArrayCollection();
        $this->maturity  = new ArrayCollection();
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
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * @param string|null $color
     */
    public function setColor(?string $color): void
    {
        $this->color = $color;
    }

    /**
     * @param Question $question
     */
    public function addQuestion(Question $question): void
    {
        $this->questions->add($question);
        $question->setDomain($this);
    }

    /**
     * @param Question $question
     */
    public function removeQuestion(Question $question): void
    {
        $this->questions->removeElement($question);
    }

    /**
     * @return iterable
     */
    public function getQuestions(): iterable
    {
        return $this->questions;
    }

    /**
     * @param Maturity $maturity
     */
    public function addMaturity(Maturity $maturity): void
    {
        $this->maturity->add($maturity);
        $maturity->setDomain($this);
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
}
