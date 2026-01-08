<?php

namespace App\Http\Requests;

use App\Http\Requests\SanitizedFormRequest;

class UpsertCartItemRequest extends SanitizedFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'book_id' => ['required', 'integer', 'exists:books,id,deleted_at,NULL'],
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
