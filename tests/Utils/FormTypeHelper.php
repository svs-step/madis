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

namespace App\Tests\Utils;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilderInterface;

class FormTypeHelper extends TestCase
{
    /**
     * Create a FormBuilder thanks to provided data.
     *
     * @param array $data The array of field to add. Key is field name, value field type.
     *
     * @return FormBuilderInterface The prophesized FormBuilderInterface
     */
    protected function prophesizeBuilder(array $data): FormBuilderInterface
    {
        $builderProphecy = $this->prophesize(FormBuilderInterface::class);

        foreach ($data as $field => $type) {
            $builderProphecy
                ->add($field, $type, Argument::type('array'))
                ->shouldBeCalled()
                ->willReturn($builderProphecy)
            ;
        }

        return $builderProphecy->reveal();
    }
}
