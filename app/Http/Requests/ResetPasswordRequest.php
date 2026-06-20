<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'token' => ['required', 'string', 'size:64'],

            'new_password' => [
                'required',
                'string',
                'min:8',
                'max:256',
                'same:confirm_new_password',
            ],

            'confirm_new_password' => [
                'required',
                'string',
            ],
        ];
    }
    public function messages(): array
    {
        return [
            'token.required' => 'Token is required.',
            'token.string' => 'Token must be a string.',
            'token.size' => 'Token must be 64 characters.',
            'new_password.required' => 'New password is required.',
            'new_password.string' => 'New password must be a string.',
            'new_password.min' => 'New password must be at least 8 characters.',
            'new_password.max' => 'New password must not exceed 256 characters.',
            'confirm_new_password.required' => 'Password confirmation is required.',
            'confirm_new_password.string' => 'Password confirmation must be a string.',
        ];
    }
}
