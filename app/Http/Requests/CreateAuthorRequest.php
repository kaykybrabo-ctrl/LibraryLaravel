<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class CreateAuthorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:authors,name'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'photo' => ['nullable', 'string', 'max:500'],
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'O nome do autor é obrigatório.',
            'name.max' => 'O nome do autor não pode ter mais de 255 caracteres.',
            'name.unique' => 'Já existe um autor com este nome.',
            'bio.max' => 'A biografia não pode ter mais de 2000 caracteres.',
            'photo.max' => 'A URL da foto não pode ter mais de 500 caracteres.',
        ];
    }
}
