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
            'id.required' => __('validation.custom.id.required'),
            'id.exists' => __('validation.custom.id.exists'),
            'title.required' => __('validation.custom.title.required'),
            'title.max' => __('validation.custom.title.max'),
            'author_id.required' => __('validation.custom.author_id.required'),
            'author_id.exists' => __('validation.custom.author_id.exists'),
            'description.max' => __('validation.custom.description.max'),
            'photo.max' => __('validation.custom.photo.max'),
            'price.numeric' => __('validation.custom.price.numeric'),
            'price.min' => __('validation.custom.price.min'),
        ];
    }
}
