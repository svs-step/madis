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

namespace App\Tests\Domain\User\Model;

use App\Domain\User\Model\Collectivity;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

class CollectivityTest extends TestCase
{
    public function testConstruct()
    {
        $model = new Collectivity();

        $this->assertInstanceOf(UuidInterface::class, $model->getId());
        $this->assertInstanceOf(ArrayCollection::class, $model->getUsers());
        $this->assertTrue($model->isActive());
    }
}
