<?php

namespace App\Domain\User\Validator\Constraints;

use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordRequirementsValidator;
use Symfony\Component\Validator\Constraint;

class CustomPasswordValidator extends PasswordRequirementsValidator
{
    private $minLength;
    private $requireCaseDiff;
    private $requireLetters;
    private $requireNumbers;
    private $requireSpecialCharacter;

    public function __construct(
        $minLength,
        $requireCaseDiff,
        $requireLetters,
        $requireNumbers,
        $requireSpecialCharacter
    ) {
        $this->minLength               = $minLength;
        $this->requireCaseDiff         = $requireCaseDiff;
        $this->requireLetters          = $requireLetters;
        $this->requireNumbers          = $requireNumbers;
        $this->requireSpecialCharacter = $requireSpecialCharacter;
    }

    /**
     * @param string|null    $value
     * @param CustomPassword $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $constraint->minLength               = $this->minLength;
        $constraint->requireCaseDiff         = $this->requireCaseDiff;
        $constraint->requireLetters          = $this->requireLetters;
        $constraint->requireNumbers          = $this->requireNumbers;
        $constraint->requireSpecialCharacter = $this->requireSpecialCharacter;

        parent::validate($value, $constraint);
    }
}
