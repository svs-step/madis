<?php

namespace App\Domain\User\Exception;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ExceededLoginAttemptsException extends AccessDeniedHttpException
{
}
