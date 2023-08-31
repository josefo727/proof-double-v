<?php

namespace App\Http\Requests;

use App\Services\OrderStatus;
use Illuminate\Validation\Rule;

class OrderUpdateRequest extends BaseFormRequest
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
     * @return array<string,array<int,mixed>>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(OrderStatus::getAcceptedValuesForTransaction())],
        ];
    }
}
