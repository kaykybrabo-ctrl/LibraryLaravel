<?php
namespace App\Http\Requests;
use App\Http\Requests\SanitizedFormRequest;
class ReviewRequest extends SanitizedFormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'book_id' => ['required','exists:books,id'],
            'rating' => ['required','integer','min:1','max:5'],
            'comment' => ['nullable','string'],
        ];
    }
}
