<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class SanitizedFormRequest extends FormRequest
{
    protected array $dontTrim = ['password', 'password_confirmation'];

    protected array $dontStripTags = ['password', 'password_confirmation', 'token', 'fileData'];

    protected function prepareForValidation(): void
    {
        $this->replace($this->sanitize($this->all()));
    }

    protected function sanitize(mixed $value, string|int|null $key = null): mixed
    {
        if (is_array($value)) {
            $sanitized = [];
            foreach ($value as $k => $v) {
                $sanitized[$k] = $this->sanitize($v, $k);
            }
            return $sanitized;
        }

        if (!is_string($value)) {
            return $value;
        }

        $k = is_string($key) ? $key : null;

        $result = $value;

        if (!$k || !in_array($k, $this->dontTrim, true)) {
            $result = trim($result);
        }

        if (!$k || !in_array($k, $this->dontStripTags, true)) {
            $result = strip_tags($result);
        }

        return $result;
    }
}
