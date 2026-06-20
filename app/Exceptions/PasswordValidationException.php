<?php

namespace App\Exceptions;

class PasswordValidationException extends ApiException
{
    public function __construct(
        string $message = "The password does not meet security requirements.",
        array $loc = ["password"],
        string $type = "password_validation_error",
        $input = null,
        array $ctx = []
    ) {
        parent::__construct($message, $loc, $type, $input, $ctx, 422);
    }
}