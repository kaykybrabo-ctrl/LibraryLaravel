<?php

namespace App\Http\Requests;

use App\Http\Requests\SanitizedFormRequest;

class UploadImageRequest extends SanitizedFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'target' => ['required', 'string', 'in:book,author,profile'],
            'filename' => ['required', 'string'],
            'fileData' => ['required', 'string'],
        ];
    }
}
