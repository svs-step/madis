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

namespace App\Tests\Domain\User\Repository;

use App\Application\DDD\Repository\CRUDRepositoryInterface;
use App\Domain\User\Repository;
use PHPUnit\Framework\TestCase;

class User extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(
            CRUDRepositoryInterface::class,
            $this->prophesize(Repository\User::class)->reveal()
        );
    }
}
