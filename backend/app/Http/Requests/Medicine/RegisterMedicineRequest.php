<?php

namespace App\Http\Requests\Medicine;

use App\Http\Requests\BaseRequest;

class RegisterMedicineRequest extends BaseRequest
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
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'dosage' => ['required', 'string'],
            'brand' => ['required', 'string'],
            'image_url' => ['nullable', 'url'],
            'stock' => ['required', 'integer', 'min:0'],
            'category_ids' => ['required', 'array'],
            'category_ids.*' => ['exists:categories,id'],
        ];
    }
}
