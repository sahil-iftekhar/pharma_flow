<?php

namespace App\Http\Requests\Medicine;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', Rule::unique('categories')->ignore($this->route('id'))],
        ];
    }
}
