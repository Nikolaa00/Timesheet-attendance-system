<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesMultipartBooleans;
use App\Rules\SectorBelongsToSubsidiary;
use App\Support\ApiRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    use NormalizesMultipartBooleans;
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
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username'),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')
            ],
            'password' => ['required', 'string', 'min:8', 'max:256'],
            'phone_number' => ['nullable', 'string', 'max:30', Rule::unique('users', 'phone_number'), 'regex:/^(?:\d{3}\s\d{3}\s\d{3}|02\s\d{3}\s\d{4})$/',],
            'subsidiary_id' => ['nullable', 'required_with:sector_id', 'exists:subsidiaries,id'],
            'sector_id' => [
                'nullable',
                'exists:sectors,id',
                new SectorBelongsToSubsidiary(subsidiaryId: $this->input('subsidiary_id')),
            ],
            'shift_id' => ['nullable', 'exists:shifts,id'],
            'signature_url' => ['nullable', 'string', 'max:255'],
            'auto_attendance' => ['required', 'boolean'],
            'role' => ['required', Rule::in(['admin', 'user'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->normalizeMultipartBooleans(['auto_attendance']);
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        if ($key !== null) {
            return $validated;
        }

        if (isset($validated['role'])) {
            $validated['role'] = ApiRole::toDb($validated['role']);
        }

        return $validated;
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'A user with this email already exists.',
            'username.unique' => 'This username is already taken.',
            'phone_number.unique' => 'This phone number is already used.',
            'subsidiary_id.exists' => 'Selected subsidiary does not exist.',
            'sector_id.exists' => 'Selected sector does not exist.',
            'shift_id.exists' => 'Selected shift does not exist.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.max' => 'Password must not exceed 256 characters.',
        ];
    }
}
