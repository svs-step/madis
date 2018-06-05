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

namespace App\Tests\Domain\Registry\Repository;

use App\Application\DDD\Repository\CRUDRepositoryInterface;
use App\Domain\Registry\Repository;
use PHPUnit\Framework\TestCase;

class TreatmentTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(
            CRUDRepositoryInterface::class,
            $this->prophesize(Repository\Treatment::class)->reveal()
        );
    }

    public function testMethodsExist()
    {
        $repository = $this->prophesize(Repository\Treatment::class)->reveal();

        $this->assertTrue(\method_exists($repository, 'findAllByCollectivity'));
    }
}
