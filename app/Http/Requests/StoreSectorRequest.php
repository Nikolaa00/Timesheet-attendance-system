<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSectorRequest extends FormRequest
{
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
            "name" => [
                "required",
                "string",
                "max:255",
                Rule::unique('sectors', 'name'),
            ],
            'subsidiary_ids' => [
                'required',
                'array',
                'min:1',
            ],
            'subsidiary_ids.*' => [
                'integer',
                'distinct',
                'exists:subsidiaries,id',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->name)) {
            $this->merge([
                'name' => trim($this->name),
            ]);
        }
    }

    public function messages(): array
    {
        return [
            "name.required" => "Name is required",
            "name.string" => "Name must be a string",
            "name.max" => "Name must not exceed 255 characters",
            "name.unique" => "A sector with this name already exists",
            'subsidiary_ids.array' => 'Subsidiary assignments must be provided as an array.',
            'subsidiary_ids.*.integer' => 'Each subsidiary assignment must be a valid ID.',
            'subsidiary_ids.*.distinct' => 'Subsidiary assignments must not contain duplicates.',
            'subsidiary_ids.*.exists' => 'One or more selected subsidiaries do not exist.',
            'subsidiary_ids.required' => 'Subsidiary id is required.',
            'subsidiary_ids.min' => 'At least one subsidiary must be selected.',
        ];
    }
}
