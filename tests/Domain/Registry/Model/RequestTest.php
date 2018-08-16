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
use App\Application\Traits\Model\SoftDeletableTrait;
use App\Domain\Registry\Model\Embeddable\RequestAnswer;
use App\Domain\Registry\Model\Embeddable\RequestApplicant;
use App\Domain\Registry\Model\Embeddable\RequestConcernedPeople;
use App\Domain\Registry\Model\Request;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

class RequestTest extends TestCase
{
    public function testConstruct()
    {
        $model = new Request();

        $this->assertInstanceOf(UuidInterface::class, $model->getId());
        $this->assertInstanceOf(RequestApplicant::class, $model->getApplicant());
        $this->assertInstanceOf(RequestConcernedPeople::class, $model->getConcernedPeople());
        $this->assertInstanceOf(RequestAnswer::class, $model->getAnswer());
        $this->assertFalse($model->isComplete());
        $this->assertFalse($model->isLegitimateApplicant());
        $this->assertFalse($model->isLegitimateRequest());
    }

    public function testTraits()
    {
        $model = new Request();

        $uses = \class_uses($model);
        $this->assertTrue(\in_array(CollectivityTrait::class, $uses));
        $this->assertTrue(\in_array(CreatorTrait::class, $uses));
        $this->assertTrue(\in_array(HistoryTrait::class, $uses));
        $this->assertTrue(\in_array(SoftDeletableTrait::class, $uses));
    }
}
