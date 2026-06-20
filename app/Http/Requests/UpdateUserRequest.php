<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesMultipartBooleans;
use App\Rules\SectorBelongsToSubsidiary;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $user = $this->route('user');

        return [
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
            'username' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($this->route('user')->id),
            ],
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($this->route('user')->id),
            ],
            'password' => ['nullable', 'string', 'min:8', 'max:256'],

            'phone_number' => [
                'sometimes',
                'nullable',
                'string',
                'max:30',
                Rule::unique('users', 'phone_number')
                    ->ignore($this->route('user')->id),
                'regex:/^(?:\d{3}\s\d{3}\s\d{3}|02\s\d{3}\s\d{4})$/',
            ],
            'subsidiary_id' => [
                'nullable',
                'required_with:sector_id',
                'exists:subsidiaries,id',
                new SectorBelongsToSubsidiary(
                    sectorId: $this->input('sector_id', $user->sector_id),
                ),
            ],
            'sector_id' => [
                'nullable',
                'exists:sectors,id',
                new SectorBelongsToSubsidiary(
                    subsidiaryId: $this->input('subsidiary_id', $user->subsidiary_id),
                ),
            ],
            'shift_id' => ['nullable', 'exists:shifts,id'],
            'signature_url' => ['nullable', 'string', 'max:255'],
            'auto_attendance' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->normalizeMultipartBooleans(['auto_attendance']);
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'A user with this email already exists.',
            'username.unique' => 'A user with this username already exists.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.max' => 'Password must not exceed 256 characters.',
        ];
    }
}
