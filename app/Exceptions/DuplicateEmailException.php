<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class DuplicateEmailException extends Exception
{
    public function __construct(string $message = 'Duplicate email address found', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
