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

use App\Domain\Registry\Model\Contractor;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

class ContractorTest extends TestCase
{
    public function testConstruct()
    {
        $model = new Contractor();

        $this->assertInstanceOf(UuidInterface::class, $model->getId());
        $this->assertFalse($model->isContractualClausesVerified());
        $this->assertFalse($model->isConform());
    }
}
