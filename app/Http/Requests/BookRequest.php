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
            'photo' => ['required','string'],
            'author_id' => ['required','exists:authors,id'],
        ];
    }
}
