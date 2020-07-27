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

namespace App\Tests\Domain\Registry\Symfony\Validator\Constraints;

use App\Domain\Registry\Dictionary\ConformiteOrganisation\ReponseDictionary;
use App\Domain\Registry\Model\ConformiteOrganisation\Reponse;
use App\Domain\Registry\Symfony\Validator\Constraints\ConformiteOrganisationReponseRaison;
use App\Domain\Registry\Symfony\Validator\Constraints\ConformiteOrganisationReponseRaisonValidator;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class ConformiteOrganisationReponseRaisonValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidator
    {
        return new ConformiteOrganisationReponseRaisonValidator();
    }

    public function testReponseIsNotNonConcerne(): void
    {
        $constraint = new ConformiteOrganisationReponseRaison();

        $reponse = new Reponse();
        $reponse->setReponse(ReponseDictionary::INEXISTANTE);

        $this->validator->validate($reponse, $constraint);
        $this->assertNoViolation();
    }

    public function testApplicantIsNonConcerneAndReponseRaisonIsEmpty(): void
    {
        $constraint = new ConformiteOrganisationReponseRaison();

        $reponse = new Reponse();
        $reponse->setReponse(ReponseDictionary::NON_CONCERNE);

        $this->validator->validate($reponse, $constraint);

        $this
            ->buildViolation($constraint->message)
            ->atPath('property.path.reponseRaison')
            ->assertRaised()
        ;
    }
}
