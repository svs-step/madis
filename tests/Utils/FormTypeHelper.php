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

namespace App\Tests\Utils;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Form\FormBuilderInterface;

class FormTypeHelper extends TestCase
{
    use ProphecyTrait;

    /**
     * Create a FormBuilder thanks to provided data.
     *
     * @param array $data The array of field to add. Key is field name, value field type.
     *
     * @return FormBuilderInterface|ObjectProphecy The prophesized FormBuilderInterface, revealled or not
     */
    protected function prophesizeBuilder(array $data, bool $reveal = true)
    {
        $builderProphecy = $this->prophesize(FormBuilderInterface::class);

        foreach ($data as $field => $type) {
            $builderProphecy
                ->add($field, $type, Argument::cetera())
                ->shouldBeCalled()
                ->willReturn($builderProphecy)
            ;
        }

        if ($reveal) {
            return $builderProphecy->reveal();
        }

        return $builderProphecy;
    }
}
