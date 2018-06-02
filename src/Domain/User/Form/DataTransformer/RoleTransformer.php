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

namespace App\Domain\User\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class RoleTransformer implements DataTransformerInterface
{
    /**
     * Transform Role given as array to string value.
     *
     * @param array $value The array of roles
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
     * Transform Role given as string to array value.
     *
     * @param mixed $value The string of roles
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
