<?php

namespace App\Http\Requests;

use App\Http\Requests\SanitizedFormRequest;

class RequestPasswordResetRequest extends SanitizedFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
        ];
    }
}
