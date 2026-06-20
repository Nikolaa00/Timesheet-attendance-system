<?php

namespace App\Support;

class ApiRole
{
    public static function toApi(?string $dbRole): ?string
    {
        return $dbRole === 'employee' ? 'user' : $dbRole;
    }

    public static function toDb(?string $apiRole): ?string
    {
        return $apiRole === 'user' ? 'employee' : $apiRole;
    }
}
