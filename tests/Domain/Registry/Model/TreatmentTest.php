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
use App\Domain\Registry\Model\Embeddable\Delay;
use App\Domain\Registry\Model\Treatment;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

class TreatmentTest extends TestCase
{
    public function testConstruct()
    {
        $model = new Treatment();

        $this->assertInstanceOf(UuidInterface::class, $model->getId());
        $this->assertEquals([], $model->getConcernedPeople());
        $this->assertFalse($model->isSensibleInformations());
        $this->assertInstanceOf(ArrayCollection::class, $model->getContractors());
        $this->assertInstanceOf(Delay::class, $model->getDelay());
        $this->assertTrue($model->isActive());
    }

    public function testTraits()
    {
        $model = new Treatment();

        $uses = \class_uses($model);
        $this->assertTrue(\in_array(CollectivityTrait::class, $uses));
        $this->assertTrue(\in_array(CreatorTrait::class, $uses));
        $this->assertTrue(\in_array(HistoryTrait::class, $uses));
    }
}
