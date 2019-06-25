<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author Donovan Bourlard <donovan@awkan.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
