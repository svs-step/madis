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

namespace App\Domain\Registry\Calculator\Completion;

abstract class AbstractCompletion
{
    abstract protected function getPoints($object): int;

    abstract protected function getMaxPoints(): int;

    /**
     * Calculate completion percent
     * Rounded to up by getting the following int number.
     *
     * @param mixed $object The object on which to calculate completion
     *
     * @return int
     */
    public function calculate($object): int
    {
        return \intval(\ceil($this->getPoints($object) / $this->getMaxPoints() * 100));
    }
}
