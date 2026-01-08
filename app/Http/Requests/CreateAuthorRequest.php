<?php
namespace App\Http\Requests;
use App\Http\Requests\SanitizedFormRequest;
class CreateAuthorRequest extends SanitizedFormRequest
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
            'name.required' => __('validation.custom.name.required'),
            'name.max' => __('validation.custom.name.max'),
            'name.unique' => __('validation.custom.name.unique'),
            'bio.max' => __('validation.custom.bio.max'),
            'photo.max' => __('validation.custom.photo.max'),
        ];
    }
}
