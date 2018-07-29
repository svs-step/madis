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

namespace App\Tests\Domain\Maturity\Model;

use App\Application\Traits\Model\CollectivityTrait;
use App\Application\Traits\Model\CreatorTrait;
use App\Application\Traits\Model\HistoryTrait;
use App\Domain\Maturity\Model\Survey;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

class SurveyTest extends TestCase
{
    public function testConstruct()
    {
        $model = new Survey();

        $this->assertInstanceOf(UuidInterface::class, $model->getId());
        $this->assertInstanceOf(ArrayCollection::class, $model->getAnswers());
        $this->assertInstanceOf(ArrayCollection::class, $model->getMaturity());
        $this->assertEquals(0, $model->getScore());
    }

    public function testTraits()
    {
        $model = new Survey();

        $uses = \class_uses($model);
        $this->assertTrue(\in_array(CollectivityTrait::class, $uses));
        $this->assertTrue(\in_array(CreatorTrait::class, $uses));
        $this->assertTrue(\in_array(HistoryTrait::class, $uses));
    }
}
