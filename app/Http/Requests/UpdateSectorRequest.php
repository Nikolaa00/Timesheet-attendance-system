<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSectorRequest extends FormRequest
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
        $sector = $this->route('sector');

        return [
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('sectors', 'name')->ignore($sector->id)
            ],
            'subsidiary_ids' => [
                'sometimes',
                'array',
            ],
            'subsidiary_ids.*' => [
                'integer',
                'distinct',
                'exists:subsidiaries,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.sometimes' => 'Name is required when present.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name must not exceed 255 characters.',
            'name.unique' => 'A sector with this name already exists',
            'subsidiary_ids.array' => 'Subsidiary assignments must be provided as an array.',
            'subsidiary_ids.*.integer' => 'Each subsidiary assignment must be a valid ID.',
            'subsidiary_ids.*.distinct' => 'Subsidiary assignments must not contain duplicates.',
            'subsidiary_ids.*.exists' => 'One or more selected subsidiaries do not exist.',
        ];
    }
}
