<?php

namespace App\Http\Requests;

use App\Rules\QuantityAvailable;
use Illuminate\Validation\Rule;

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
        $rules = [
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

        // Validate that there are no duplicate product_id's.
        $items = $this->input('items', []);
        $productIds = array_column($items, 'product_id');
        if (count(array_unique($productIds)) < count($productIds)) {
            $rules[] = [
                'items.*.product_id' => Rule::unique()->where(function ($query) {
                    return $query->whereIn('product_id', $this->input('items.*.product_id', []));
                }),
            ];
        }

        return $rules;
    }
}
