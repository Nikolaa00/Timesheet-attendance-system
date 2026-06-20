<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Subsidiary;

class UpdateSubsidiaryRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:30',
            'address' => 'sometimes|required|string|max:255',
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
            'email' => is_string($this->email)
                ? strtolower(trim($this->email))
                : $this->email,

            'phone_number' => is_string($this->phone_number)
                ? trim($this->phone_number)
                : $this->phone_number,
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            if ($validator->errors()->any()) {
                return;
            }

            $subsidiary = $this->route('subsidiary');

            $name = trim((string) ($this->input('name') ?? $subsidiary->name));
            $address = trim((string) ($this->input('address') ?? $subsidiary->address));
            $phoneNumber = $this->filled('phone_number') ? trim((string) $this->phone_number) : $subsidiary->phone_number;
            $email = $this->filled('email') ? trim((string) $this->email) : $subsidiary->email;

            if (
                $name === $subsidiary->name &&
                $address === $subsidiary->address &&
                $phoneNumber === $subsidiary->phone_number &&
                $email === $subsidiary->email
            ) {
                $validator->errors()->add('name', 'No changes detected. Update not required.');
                return;
            }

            $duplicateExists = Subsidiary::where('id', '!=', $subsidiary->id)
                ->where('name', $name)
                ->where('address', $address)
                ->exists();

            if ($duplicateExists) {
                $validator->errors()->add(
                    'name',
                    'Subsidiary could not be created because related data is invalid or already exists'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            // Fixed: Changed '.sometimes' keys to '.required'
            'name.required' => 'Name is required when present.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name must not exceed 30 characters.',
            'address.required' => 'Address is required when present.',
            'address.string' => 'Address must be a string.',
            'address.max' => 'Address must not exceed 255 characters.',
            'phone_number.string' => 'Phone must be a string.',
            'phone_number.max' => 'Phone must not exceed 30 characters.',
            'phone_number.regex' => 'Phone may only contain digits, one optional leading plus sign, spaces, and dashes.',
            'email.string' => 'Email must be a string.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email must not exceed 255 characters.',
            'email.regex' => 'Email may only contain Latin letters, numbers, and valid email symbols.',
        ];
    }
}
