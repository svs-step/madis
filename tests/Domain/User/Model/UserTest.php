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

use App\Domain\User\Model\User;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(UserInterface::class, new User());
    }

    public function testConstruct()
    {
        $model = new User();

        $this->assertInstanceOf(UuidInterface::class, $model->getId());
        $this->assertEquals([], $model->getRoles());
        $this->assertTrue($model->isEnabled());
    }
}
