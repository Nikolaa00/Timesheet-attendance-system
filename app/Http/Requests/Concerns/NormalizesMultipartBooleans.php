<?php

namespace App\Http\Requests\Concerns;

trait NormalizesMultipartBooleans
{
    protected function normalizeMultipartBooleans(array $fields): void
    {
        $normalized = [];

        foreach ($fields as $field) {
            if ($this->exists($field)) {
                $normalized[$field] = $this->boolean($field);
            }
        }

        if ($normalized !== []) {
            $this->merge($normalized);
        }
    }
}
