<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Subsidiary;

class StoreSubsidiaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:30',
            'address' => 'required|string|max:255',
            'phone_number' => [
                'nullable',
                'string',
                'max:30',
                'regex:/^(?:\d{3}\s\d{3}\s\d{3}|02\s\d{3}\s\d{4})$/',
            ],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                'regex:/^[A-Za-z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[A-Za-z0-9-]+(?:\.[A-Za-z0-9-]+)+$/',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => is_string($this->email) ? strtolower(trim($this->email)) : $this->email,
            'phone_number' => is_string($this->phone_number) ? trim($this->phone_number) : $this->phone_number,
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $exists = Subsidiary::where('name', trim($this->name))
                ->where('address', trim($this->address))
                ->exists();

            if ($exists) {
                $validator->errors()->add(
                    'subsidiary',
                    'Subsidiary could not be created because related data is invalid or already exists'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'name.string' => 'Name must be a string',
            'name.max' => 'Name must not exceed 30 characters',
            'address.required' => 'Address is required',
            'address.string' => 'Address must be a string',
            'address.max' => 'Address must not exceed 255 characters',
            'phone_number.string' => 'Phone must be a string',
            'phone_number.max' => 'Phone must not exceed 30 characters',
            'email.nullable' => 'Email is optional',
            'phone_number.regex' => 'Phone may only contain digits, one optional leading plus sign, spaces, and dashes.',
            'email.optional' => 'Email is optional',
            'email.string' => 'Email must be a string',
            'email.email' => 'Email must be a valid email address',
            'email.max' => 'Email must not exceed 255 characters',
            'email.regex' => 'Email may only contain Latin letters, numbers, and valid email symbols.',
        ];
    }
}
