<?php

namespace App\Http\Requests;

use App\Http\Requests\SanitizedFormRequest;

class RemoveCartItemRequest extends SanitizedFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'book_id' => ['required', 'integer'],
        ];
    }
}
