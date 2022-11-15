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

namespace App\Domain\User\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class MoreInfoTransformer implements DataTransformerInterface
{
    /**
     * Transform MoreInfo given as array to string value.
     *
     * @param array $value The array of moreInfos
     *
     * @return string|null The parsed array to string
     */
    public function transform($value): ?string
    {
        if (\is_null($value)) {
            return null;
        }

        if (!\is_array($value)) {
            throw new TransformationFailedException("Value of type array expected, '{$value}' given");
        }

        return \implode(', ', $value);
    }

    /**
     * Transform MoreInfo given as string to array value.
     *
     * @param mixed $value The string of moreInfos
     *
     * @return array The parsed string to array
     */
    public function reverseTransform($value): array
    {
        if (\is_null($value)) {
            return [];
        }

        return [$value];
    }
}
