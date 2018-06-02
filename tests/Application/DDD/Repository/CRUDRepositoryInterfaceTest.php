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

namespace App\Tests\Application\DDD\Repository;

use App\Application\DDD\Repository\CRUDRepositoryInterface;
use App\Application\DDD\Repository\RepositoryInterface;
use PHPUnit\Framework\TestCase;

class CRUDRepositoryInterfaceTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(
            RepositoryInterface::class,
            $this->prophesize(CRUDRepositoryInterface::class)->reveal()
        );
    }
}
