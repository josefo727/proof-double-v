<?php

namespace App\Http\Requests;

class CustomerStoreRequest extends BaseFormRequest
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
            'name' => ['required', 'max:100', 'string'],
            'email' => ['required', 'email', 'unique:customers,email'],
            'phone' => ['required', 'max:20', 'string'],
        ];
    }
}
