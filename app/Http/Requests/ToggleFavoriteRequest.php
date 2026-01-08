<?php

namespace App\Http\Requests;

use App\Http\Requests\SanitizedFormRequest;

class ToggleFavoriteRequest extends SanitizedFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id,deleted_at,NULL'],
            'book_id' => ['required', 'integer', 'exists:books,id,deleted_at,NULL'],
        ];
    }
}
