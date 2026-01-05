<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class UpdateBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'id' => ['required', 'exists:books,id'],
            'title' => ['required', 'string', 'max:255'],
            'author_id' => ['required', 'exists:authors,id'],
            'description' => ['nullable', 'string', 'max:2000'],
            'photo' => ['nullable', 'string', 'max:500'],
            'price' => ['nullable', 'numeric', 'min:0'],
        ];
    }
    public function messages(): array
    {
        return [
            'id.required' => 'O ID do livro é obrigatório.',
            'id.exists' => 'O livro selecionado não existe.',
            'title.required' => 'O título é obrigatório.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'author_id.required' => 'O autor é obrigatório.',
            'author_id.exists' => 'O autor selecionado não existe.',
            'description.max' => 'A descrição não pode ter mais de 2000 caracteres.',
            'photo.max' => 'A URL da foto não pode ter mais de 500 caracteres.',
            'price.numeric' => 'O preço deve ser um número.',
            'price.min' => 'O preço não pode ser negativo.',
        ];
    }
}
