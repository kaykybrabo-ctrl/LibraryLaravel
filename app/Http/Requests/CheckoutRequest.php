<?php

namespace App\Http\Requests;

use App\Http\Requests\SanitizedFormRequest;

class CheckoutRequest extends SanitizedFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.book_id' => ['required', 'integer', 'exists:books,id,deleted_at,NULL'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
