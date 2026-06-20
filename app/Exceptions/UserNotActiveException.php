<?php

namespace App\Exceptions;

class UserNotActiveException extends ApiException
{
    public function __construct(
        string $message = "The user account is currently inactive.",
        array $loc = ["user"],
        string $type = "user_not_active_error",
        $input = null,
        array $ctx = []
    ) {
        parent::__construct($message, $loc, $type, $input, $ctx, 403);
    }
}