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

use App\Application\Traits\Model\CollectivityTrait;
use App\Application\Traits\Model\CreatorTrait;
use App\Application\Traits\Model\HistoryTrait;
use App\Domain\Registry\Model\Mesurement;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

class MesurementTest extends TestCase
{
    public function testConstruct()
    {
        $model = new Mesurement();

        $this->assertInstanceOf(UuidInterface::class, $model->getId());
    }

    public function testTraits()
    {
        $model = new Mesurement();

        $uses = \class_uses($model);
        $this->assertTrue(\in_array(CollectivityTrait::class, $uses));
        $this->assertTrue(\in_array(CreatorTrait::class, $uses));
        $this->assertTrue(\in_array(HistoryTrait::class, $uses));
    }
}
