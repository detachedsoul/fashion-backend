<?php

namespace App\Http\Requests\Concerns;

trait ConvertsBooleans
{
    protected array $booleanFields = [
        'is_custom_only',
        'is_active',
        'is_featured',
    ];

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach ($this->booleanFields as $field) {
            if ($this->has($field)) {
                $data[$field] = $this->boolean($field);
            }
        }

        $this->merge($data);
    }
}
