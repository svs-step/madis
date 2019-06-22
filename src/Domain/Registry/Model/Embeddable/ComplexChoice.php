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

namespace App\Domain\Registry\Model\Embeddable;

class ComplexChoice
{
    /**
     * @var bool
     */
    private $check;

    /**
     * @var string|null
     */
    private $comment;

    public function __construct()
    {
        $this->check = false;
    }

    /**
     * @return bool
     */
    public function isCheck(): bool
    {
        return $this->check;
    }

    /**
     * @param bool $check
     */
    public function setCheck(bool $check): void
    {
        $this->check = $check;
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
