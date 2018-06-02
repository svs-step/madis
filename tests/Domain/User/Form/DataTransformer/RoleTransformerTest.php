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
