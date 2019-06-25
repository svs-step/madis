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

namespace App\Tests\Domain\User\Form\DataTransformer;

use App\Domain\User\Form\DataTransformer\RoleTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class RoleTransformerTest extends TestCase
{
    /**
     * Test RoleTransformer heritage.
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf(DataTransformerInterface::class, new RoleTransformer());
    }

    /**
     * Test transform method
     * Value is null.
     */
    public function testTransformWithNullValue()
    {
        $this->assertNull((new RoleTransformer())->transform(null));
    }

    /**
     * Test transform method
     * Value is not an array.
     */
    public function testTransformWithNonArrayValue()
    {
        $this->expectException(TransformationFailedException::class);

        (new RoleTransformer())->transform('ThisIsAStringAndNotAnArray');
    }

    /**
     * Test transform method
     * Value is an array, as expected.
     */
    public function testTransform()
    {
        $string = 'Foo';
        $this->assertEquals(
            $string,
            (new RoleTransformer())->transform([$string])
        );
    }

    /**
     * Test reverseTransform method
     * Value is null.
     */
    public function testReverseTransformNullValue()
    {
        $this->assertEquals([], (new RoleTransformer())->reverseTransform(null));
    }

    /**
     * Test reverseTransform method
     * Value is a string.
     */
    public function testReverseTransform()
    {
        $string = 'Foo';
        $this->assertEquals([$string], (new RoleTransformer())->reverseTransform($string));
    }
}
