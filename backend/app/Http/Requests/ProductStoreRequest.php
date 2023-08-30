<?php

namespace App\Http\Requests;

class ProductStoreRequest extends BaseFormRequest
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
     * @return array<string,array<int,string>>
     */
    public function rules(): array
    {
        return [
            'sku' => ['required', 'unique:products,sku', 'max:50', 'string'],
            'name' => ['required', 'max:50', 'string'],
            'price' => ['required', 'numeric'],
            'quantity' => ['required', 'numeric'],
            'user_id' => ['required', 'exists:users,id'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => auth()->id(),
        ]);
    }
}
