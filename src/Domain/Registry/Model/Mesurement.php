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
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Mesurement
{
    use CollectivityTrait;
    use CreatorTrait;
    use HistoryTrait;

    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * FR: Nom.
     *
     * @var string|null
     */
    private $name;

    /**
     * FR: Type.
     *
     * @var string|null
     */
    private $type;

    /**
     * FR: Description.
     *
     * @var string|null
     */
    private $description;

    /**
     * FR: Cout.
     *
     * @var string|null
     */
    private $cost;

    /**
     * FR: Charge.
     *
     * @var string|null
     */
    private $charge;

    /**
     * FR: Statut.
     *
     * @var string|null
     */
    private $status;

    /**
     * FR: Date de planification.
     *
     * @var \DateTime|null
     */
    private $planificationDate;

    /**
     * @var string|null
     */
    private $comment;

    /**
     * Mesurement constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if (\is_null($this->getName())) {
            return '';
        }

        if (\strlen($this->getName()) > 50) {
            return \substr($this->getName(), 0, 50) . '...';
        }

        return $this->getName();
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
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getCost(): ?string
    {
        return $this->cost;
    }

    /**
     * @param string|null $cost
     */
    public function setCost(?string $cost): void
    {
        $this->cost = $cost;
    }

    /**
     * @return string|null
     */
    public function getCharge(): ?string
    {
        return $this->charge;
    }

    /**
     * @param string|null $charge
     */
    public function setCharge(?string $charge): void
    {
        $this->charge = $charge;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     */
    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return \DateTime|null
     */
    public function getPlanificationDate(): ?\DateTime
    {
        return $this->planificationDate;
    }

    /**
     * @param \DateTime|null $planificationDate
     */
    public function setPlanificationDate(?\DateTime $planificationDate): void
    {
        $this->planificationDate = $planificationDate;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     */
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }
}
