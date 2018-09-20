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

namespace App\Domain\User\Component;

use RandomLib\Factory;
use RandomLib\Generator;

class TokenGenerator
{
    const TOKEN_LENGTH = 50;
    /**
     * @var Factory
     */
    private $factory;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @return string
     */
    public function generateToken()
    {
        return $this->factory
            ->getMediumStrengthGenerator()
            ->generateString(
                self::TOKEN_LENGTH,
                Generator::CHAR_DIGITS + Generator::CHAR_LOWER + Generator::CHAR_UPPER
            );
    }
}
