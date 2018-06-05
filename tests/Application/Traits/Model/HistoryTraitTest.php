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

namespace App\Tests\Application\Traits\Model;

use App\Application\Traits\Model\HistoryTrait;
use PHPUnit\Framework\TestCase;

class HistoryTraitTest extends TestCase
{
    /**
     * Test if expected properties exists.
     */
    public function testPropertyExistence()
    {
        $object = new class() {
            use HistoryTrait;
        };

        $this->assertTrue(\property_exists($object, 'createdAt'));
        $this->assertTrue(\property_exists($object, 'updatedAt'));
    }

    /**
     * Test if expected methods exists.
     */
    public function testMethodsExistence()
    {
        $object = new class() {
            use HistoryTrait;
        };

        $this->assertTrue(\method_exists($object, 'getCreatedAt'));
        $this->assertTrue(\method_exists($object, 'setCreatedAt'));
        $this->assertTrue(\method_exists($object, 'getUpdatedAt'));
        $this->assertTrue(\method_exists($object, 'setUpdatedAt'));
    }
}
