<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class LoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'user_id' => ['required','exists:users,id'],
            'book_id' => ['required','exists:books,id'],
            'loan_date' => ['required','date','after_or_equal:today'],
            'return_date' => ['required','date','after:loan_date','before_or_equal:2030-12-31'],
        ];
    }
}
