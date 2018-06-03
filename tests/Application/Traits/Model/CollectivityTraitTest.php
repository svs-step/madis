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

use App\Application\Traits\Model\CollectivityTrait;
use PHPUnit\Framework\TestCase;

class CollectivityTraitTest extends TestCase
{
    /**
     * Test if expected properties exists.
     */
    public function testPropertyExistence()
    {
        $object = new class() {
            use CollectivityTrait;
        };

        $this->assertTrue(\property_exists($object, 'collectivity'));
    }

    /**
     * Test if expected methods exists.
     */
    public function testMethodsExistence()
    {
        $object = new class() {
            use CollectivityTrait;
        };

        $this->assertTrue(\method_exists($object, 'getCollectivity'));
        $this->assertTrue(\method_exists($object, 'setCollectivity'));
    }
}
