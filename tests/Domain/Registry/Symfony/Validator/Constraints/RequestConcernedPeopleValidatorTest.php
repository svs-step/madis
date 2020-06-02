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

use App\Domain\Registry\Model\Request;
use App\Domain\Registry\Symfony\Validator\Constraints\RequestConcernedPeople;
use App\Domain\Registry\Symfony\Validator\Constraints\RequestConcernedPeopleValidator;
use Symfony\Component\Form\Form;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class RequestConcernedPeopleValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidator
    {
        return new RequestConcernedPeopleValidator();
    }

    public function testApplicantIsConcernedPeople(): void
    {
        $constraint = new RequestConcernedPeople();

        $request = new Request();
        $request->getApplicant()->setConcernedPeople(true);

        $form = $this->prophesize(Form::class);
        $form->getData()->shouldBeCalled()->willReturn($request);
        $this->setRoot($form->reveal());

        $this->validator->validate(new \App\Domain\Registry\Model\Embeddable\RequestConcernedPeople(), $constraint);
        $this->assertNoViolation();
    }

    public function testApplicantIsNotConcernedPeopleAndForgotFirstName(): void
    {
        $constraint = new RequestConcernedPeople();

        $request = new Request();
        $request->getApplicant()->setConcernedPeople(false);

        $form = $this->prophesize(Form::class);
        $form->getData()->shouldBeCalled()->willReturn($request);
        $this->setRoot($form->reveal());

        $this->validator->validate(new \App\Domain\Registry\Model\Embeddable\RequestConcernedPeople(), $constraint);
        $this
            ->buildViolation($constraint->message)
            ->atPath('property.path.firstName')
            ->buildNextViolation($constraint->message)
            ->atPath('property.path.lastName')
            ->assertRaised()
            ;
    }
}
