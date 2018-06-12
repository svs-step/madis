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

namespace App\Tests\Domain\User\Component;

use App\Domain\User\Component\TokenGenerator;
use PHPUnit\Framework\TestCase;
use RandomLib\Factory;

class TokenGeneratorTest extends TestCase
{
    /**
     * Test generateToken method.
     *
     * It return a token
     */
    public function testGenerateToken()
    {
        $token = (new TokenGenerator(new Factory()))->generateToken();

        $this->assertEquals(TokenGenerator::TOKEN_LENGTH, \strlen($token));
    }
}
