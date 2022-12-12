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

namespace App\Domain\Registry\Symfony\Validator\Constraints;

use App\Domain\Registry\Dictionary\ConformiteOrganisation\ReponseDictionary;
use App\Domain\Registry\Model\ConformiteOrganisation\Reponse;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ConformiteOrganisationReponseRaisonValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ConformiteOrganisationReponseRaison) {
            throw new UnexpectedTypeException($constraint, ConformiteOrganisationReponseRaison::class);
        }

        if (!$value instanceof Reponse) {
            return;
        }

        if (ReponseDictionary::NON_CONCERNE === $value->getReponse() && empty($value->getReponseRaison())) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('reponseRaison')
                ->addViolation();
        }
    }
}
