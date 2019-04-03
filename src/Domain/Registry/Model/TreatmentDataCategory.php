<?php

declare(strict_types=1);

namespace App\Domain\Registry\Model;

class TreatmentDataCategory
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $position;

    /**
     * @var bool
     */
    private $sensible;

    public function __construct(string $code, string $name, int $position, bool $sensible = false)
    {
        $this->code     = $code;
        $this->name     = $name;
        $this->position = $position;
        $this->sensible = $sensible;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return bool
     */
    public function isSensible(): bool
    {
        return $this->sensible;
    }
}
