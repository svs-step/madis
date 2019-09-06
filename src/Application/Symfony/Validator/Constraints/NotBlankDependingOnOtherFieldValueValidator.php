<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author ANODE <contact@agence-anode.fr>
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

namespace App\Application\Symfony\Validator\Constraints;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NotBlankDependingOnOtherFieldValueValidator extends ConstraintValidator
{
    /**
     * @var PropertyAccessorInterface
     */
    private $accessor;

    public function __construct(PropertyAccessorInterface $accessor)
    {
        $this->accessor = $accessor;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof NotBlankDependingOnOtherFieldValue) {
            throw new UnexpectedTypeException($constraint, NotBlankDependingOnOtherFieldValue::class);
        }

        $otherFieldValue = $this->accessor->getValue($this->context->getObject(), $constraint->otherFieldPath);

        // Only make validation if other field value is the expected one
        if ($constraint->otherFieldExpectedValue !== $otherFieldValue) {
            return;
        }

        // Since the other value is the expected one, check if value is not blank
        if (false === $value || (empty($value) && '0' != $value)) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
