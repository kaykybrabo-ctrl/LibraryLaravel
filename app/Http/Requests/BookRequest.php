<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required','string'],
            'description' => ['required','string'],
            'photo' => ['nullable','string'],
            'author_id' => ['nullable','integer','exists:authors,id'],
            'author_name' => ['required_without:author_id','string'],
            'author_bio' => ['nullable','string'],
            'author_photo' => ['nullable','string'],
        ];
    }
}
