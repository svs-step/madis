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

namespace App\Tests\Domain\Registry\Model;

use App\Domain\Registry\Model\Treatment;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

class TreatmentTest extends TestCase
{
    public function testConstruct()
    {
        $model = new Treatment();

        $this->assertInstanceOf(UuidInterface::class, $model->getId());
    }
}
