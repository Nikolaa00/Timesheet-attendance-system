<?php
namespace App\Http\Services;
use Illuminate\Validation\Rules\Password;
use App\Exceptions\PasswordValidationException;

class PasswordValidationService
{
    public static function rules(): array
    {
        return [
            'required',
            'string',
            'max:256',
            Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols(),
        ];
    }

    public static function validate(string $password): void
    {
        $validator = validator(
            ['password' => $password],
            ['password' => self::rules()]
        );

        if ($validator->fails()) {
            throw new PasswordValidationException(
                message: $validator->errors()->first('password')
            );
        }
    }
}