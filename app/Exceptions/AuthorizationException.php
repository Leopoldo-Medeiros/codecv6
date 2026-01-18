<?php

namespace App\Exceptions;

use Exception;

class AuthorizationException extends Exception
{
    public function __construct(string $message = 'You are not authorized to perform this action', int $code = 403)
    {
        parent::__construct($message, $code);
    }
}
