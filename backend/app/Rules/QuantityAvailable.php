<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Product;

class QuantityAvailable implements ValidationRule
{
    /**
     * @param mixed $productId
     */
    public function __construct(private $productId)
    {
        $productId = $productId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $product = Product::query()->find($this->productId);
        if (!$product) {
            return;
        }

        if ($value > $product->quantity) {
            $fail("La cantidad solicitada para el producto: '{$product->sku} - {$product->name}' excede el stock disponible");
        }
    }
}
