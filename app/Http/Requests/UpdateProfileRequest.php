<?php
namespace App\Http\Requests;
use App\Http\Requests\SanitizedFormRequest;
class UpdateProfileRequest extends SanitizedFormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'name' => ['required','string'],
            'photo' => ['nullable','string'],
        ];
    }
}
