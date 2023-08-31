<?php

namespace App\Http\Requests;

use App\Rules\QuantityAvailable;
use Illuminate\Validation\Validator;

class OrderStoreRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string,mixed>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'items' => ['required', 'array'],
            'items.*.product_id' => [
                'required',
                'exists:products,id',
            ],
            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    preg_match('/\d+/', $attribute, $matches);
                    $index = $matches[0];
                    $productId = $this->input("items.$index.product_id");
                    $rule = new QuantityAvailable($productId);
                    $rule->validate($attribute, $value, $fail);
                },
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);
            $productIds = array_column($items, 'product_id');

            if (count(array_unique($productIds)) < count($productIds)) {
                $validator->errors()->add('items.*.product_id', 'El id del producto debe ser único dentro del array items');
            }
        });
    }
}
