<?php
namespace App\Http\Requests;
use App\Http\Requests\SanitizedFormRequest;
class BorrowBookRequest extends SanitizedFormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'book_id' => ['required', 'exists:books,id'],
            'return_date' => ['required', 'date', 'after:today'],
        ];
    }
    public function messages(): array
    {
        return [
            'book_id.required' => __('validation.custom.book_id.required'),
            'book_id.exists' => __('validation.custom.book_id.exists'),
            'return_date.required' => __('validation.custom.return_date.required'),
            'return_date.date' => __('validation.custom.return_date.date'),
            'return_date.after' => __('validation.custom.return_date.after'),
        ];
    }
}
