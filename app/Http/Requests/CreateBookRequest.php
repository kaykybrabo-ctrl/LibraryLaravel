<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class CreateBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (!$this->filled('author_name') && $this->filled('new_author_name')) {
            $this->merge([
                'author_name' => $this->input('new_author_name'),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'author_id' => ['nullable', 'exists:authors,id', 'required_without_all:author_name,new_author_name'],
            'description' => ['nullable', 'string', 'max:2000'],
            'photo' => ['nullable', 'string', 'max:500'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'author_name' => ['nullable', 'string', 'max:255', 'required_without_all:author_id,new_author_name'],
            'author_bio' => ['nullable', 'string', 'max:2000'],
            'author_photo' => ['nullable', 'string', 'max:500'],
            'new_author_name' => ['nullable', 'string', 'max:255', 'required_without_all:author_id,author_name'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => __('validation.custom.title.required'),
            'title.max' => __('validation.custom.title.max'),
            'author_id.required_without_all' => __('validation.custom.author_id.required_without_all'),
            'author_id.exists' => __('validation.custom.author_id.exists'),
            'description.max' => __('validation.custom.description.max'),
            'photo.max' => __('validation.custom.photo.max'),
            'price.numeric' => __('validation.custom.price.numeric'),
            'price.min' => __('validation.custom.price.min'),
            'author_name.required_without_all' => __('validation.custom.author_name.required_without_all'),
            'author_bio.max' => __('validation.custom.author_bio.max'),
            'author_photo.max' => __('validation.custom.author_photo.max'),
            'new_author_name.required_without_all' => __('validation.custom.new_author_name.required_without_all'),
        ];
    }
}
