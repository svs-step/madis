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

namespace App\Tests\Application\Symfony\Validator\Constraints;

use App\Application\Symfony\Validator\Constraints\NotBlankDependingOnOtherFieldValue;
use App\Application\Symfony\Validator\Constraints\NotBlankDependingOnOtherFieldValueValidator;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class NotBlankDependingOnOtherFieldValueValidatorTest extends ConstraintValidatorTestCase
{
    const EXPECTED_OTHER_FIELD_VALUE   = 'expected_other_field_value';
    const UNEXPECTED_OTHER_FIELD_VALUE = 'unexpected_other_field_value';

    /**
     * @var PropertyAccessorInterface
     */
    private $accessor;

    protected function createValidator(): ConstraintValidator
    {
        $this->accessor = PropertyAccess::createPropertyAccessor();

        return new NotBlankDependingOnOtherFieldValueValidator(
            $this->accessor
        );
    }

    /**
     * Set an object & reinitialize Context + validator.
     *
     * @param mixed $data The data to use for object
     */
    protected function initObject($data): void
    {
        $this->object = $data;

        $this->context   = $this->createContext();
        $this->validator = $this->createValidator();
        $this->validator->initialize($this->context);
    }

    public function dataProviderValidObject(): array
    {
        $dataProvider = [];

        // OBJECT 1 : Expected other value, then field is not blank (string)
        $otherFieldValue      = self::EXPECTED_OTHER_FIELD_VALUE;
        $unusedFieldValue     = 'foo';
        $fieldToValidateValue = 'this-is-a-string-value';
        $data                 = new ObjectToValidateNotBlankDependingOnOtherValue();
        $data->setOtherField($otherFieldValue);
        $data->setUnusedField($unusedFieldValue);
        $data->setFieldToValidate($fieldToValidateValue);
        $dataProvider[] = [$data];

        // OBJECT 2 : Expected other value, then field is not blank (array)
        $otherFieldValue      = self::EXPECTED_OTHER_FIELD_VALUE;
        $unusedFieldValue     = 'foo';
        $fieldToValidateValue = ['this-is-an-array-value'];
        $data                 = new ObjectToValidateNotBlankDependingOnOtherValue();
        $data->setOtherField($otherFieldValue);
        $data->setUnusedField($unusedFieldValue);
        $data->setFieldToValidate($fieldToValidateValue);
        $dataProvider[] = [$data];

        // OBJECT 3 : Unexpected other value, then field can be blank
        $otherFieldValue      = self::UNEXPECTED_OTHER_FIELD_VALUE;
        $unusedFieldValue     = 'foo';
        $fieldToValidateValue = null;
        $data                 = new ObjectToValidateNotBlankDependingOnOtherValue();
        $data->setOtherField($otherFieldValue);
        $data->setUnusedField($unusedFieldValue);
        $data->setFieldToValidate($fieldToValidateValue);
        $dataProvider[] = [$data];

        // OBJECT 4 : null other value, then field can be blank
        $otherFieldValue      = null;
        $unusedFieldValue     = 'foo';
        $fieldToValidateValue = null;
        $data                 = new ObjectToValidateNotBlankDependingOnOtherValue();
        $data->setOtherField($otherFieldValue);
        $data->setUnusedField($unusedFieldValue);
        $data->setFieldToValidate($fieldToValidateValue);
        $dataProvider[] = [$data];

        return $dataProvider;
    }

    /**
     * Test validate
     * With every valid values (check data provider information).
     *
     * @dataProvider dataProviderValidObject
     *
     * @param ObjectToValidateNotBlankDependingOnOtherValue $data
     */
    public function testValidValues(ObjectToValidateNotBlankDependingOnOtherValue $data): void
    {
        $constraint = new NotBlankDependingOnOtherFieldValue(
            [
                'otherFieldPath'          => 'otherField',
                'otherFieldExpectedValue' => self::EXPECTED_OTHER_FIELD_VALUE,
            ]
        );

        $this->initObject($data);
        $this->validator->validate($data->getFieldToValidate(), $constraint);
        $this->assertNoViolation();
    }

    public function dataProviderInvalidValue(): array
    {
        $dataProvider = [];

        // OBJECT 1 : Expected other value, but field is blank (null)
        $otherFieldValue      = self::EXPECTED_OTHER_FIELD_VALUE;
        $unusedFieldValue     = 'foo';
        $fieldToValidateValue = null;
        $data                 = new ObjectToValidateNotBlankDependingOnOtherValue();
        $data->setOtherField($otherFieldValue);
        $data->setUnusedField($unusedFieldValue);
        $data->setFieldToValidate($fieldToValidateValue);
        $dataProvider[] = [$data];

        // OBJECT 2 : Expected other value, but field is blank (string)
        $otherFieldValue      = self::EXPECTED_OTHER_FIELD_VALUE;
        $unusedFieldValue     = 'foo';
        $fieldToValidateValue = '';
        $data                 = new ObjectToValidateNotBlankDependingOnOtherValue();
        $data->setOtherField($otherFieldValue);
        $data->setUnusedField($unusedFieldValue);
        $data->setFieldToValidate($fieldToValidateValue);
        $dataProvider[] = [$data];

        // OBJECT 3 : Expected other value, but field is blank (array)
        $otherFieldValue      = self::EXPECTED_OTHER_FIELD_VALUE;
        $unusedFieldValue     = 'foo';
        $fieldToValidateValue = [];
        $data                 = new ObjectToValidateNotBlankDependingOnOtherValue();
        $data->setOtherField($otherFieldValue);
        $data->setUnusedField($unusedFieldValue);
        $data->setFieldToValidate($fieldToValidateValue);
        $dataProvider[] = [$data];

        return $dataProvider;
    }

    /**
     * Test validate
     * With every invalid values (check data provider information).
     *
     * @dataProvider dataProviderInvalidValue
     *
     * @param ObjectToValidateNotBlankDependingOnOtherValue $data
     */
    public function testInvalidValues(ObjectToValidateNotBlankDependingOnOtherValue $data): void
    {
        $errorMessage = 'ThisIsErrorMessageToCatch';

        $this->initObject($data);

        $constraint = new NotBlankDependingOnOtherFieldValue(
            [
                'otherFieldPath'          => 'otherField',
                'otherFieldExpectedValue' => self::EXPECTED_OTHER_FIELD_VALUE,
                'message'                 => $errorMessage,
            ]
        );

        $this->validator->validate($data->getFieldToValidate(), $constraint);
        $this->buildViolation($errorMessage)->assertRaised();
    }
}

class ObjectToValidateNotBlankDependingOnOtherValue
{
    /** @var string|null */
    private $otherField;

    /** @var string|null */
    private $unusedField;

    /** @var mixed|null */
    private $fieldToValidate;

    /**
     * @return string|null
     */
    public function getOtherField(): ?string
    {
        return $this->otherField;
    }

    /**
     * @param string|null $otherField
     */
    public function setOtherField(?string $otherField): void
    {
        $this->otherField = $otherField;
    }

    /**
     * @return string|null
     */
    public function getUnusedField(): ?string
    {
        return $this->unusedField;
    }

    /**
     * @param string|null $unusedField
     */
    public function setUnusedField(?string $unusedField): void
    {
        $this->unusedField = $unusedField;
    }

    /**
     * @return mixed|null
     */
    public function getFieldToValidate()
    {
        return $this->fieldToValidate;
    }

    /**
     * @param mixed|null $fieldToValidate
     */
    public function setFieldToValidate($fieldToValidate): void
    {
        $this->fieldToValidate = $fieldToValidate;
    }
}
