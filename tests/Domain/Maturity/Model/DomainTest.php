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

use App\Domain\Maturity\Model\Domain;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

class DomainTest extends TestCase
{
    public function testConstruct()
    {
        $model = new Domain();

        $this->assertInstanceOf(UuidInterface::class, $model->getId());
        $this->assertInstanceOf(ArrayCollection::class, $model->getQuestions());
        $this->assertInstanceOf(ArrayCollection::class, $model->getMaturity());
    }
}
