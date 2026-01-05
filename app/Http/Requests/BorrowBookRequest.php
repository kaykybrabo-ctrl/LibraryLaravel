<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class BorrowBookRequest extends FormRequest
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
            'book_id.required' => 'O ID do livro é obrigatório.',
            'book_id.exists' => 'O livro selecionado não existe.',
            'return_date.required' => 'A data de devolução é obrigatória.',
            'return_date.date' => 'A data de devolução deve ser uma data válida.',
            'return_date.after' => 'A data de devolução deve ser posterior a hoje.',
        ];
    }
}
