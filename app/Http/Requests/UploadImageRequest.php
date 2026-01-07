<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadImageRequest extends FormRequest
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
