<?php

namespace App\Http\Requests\Medicine;

use App\Http\Requests\BaseRequest;

class UpdateMedicineRequest extends BaseRequest
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
            'name' => ['sometimes', 'required', 'string'],
            'description' => ['sometimes', 'nullable', 'string'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'dosage' => ['sometimes', 'required', 'string'],
            'brand' => ['sometimes', 'required', 'string'],
            'image_url' => ['sometimes', 'nullable', 'url'],
            'stock' => ['sometimes', 'required', 'integer', 'min:0'],
            'category_ids' => ['sometimes', 'required', 'array'],
            'category_ids.*' => ['exists:categories,id'],
        ];
    }
}
