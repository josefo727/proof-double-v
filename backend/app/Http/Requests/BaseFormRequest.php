<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class BaseFormRequest extends FormRequest
{
    protected function failedValidation(Validator $validator): void
    {
        $errors = (new ValidationException($validator))->errors();

        $message_error = collect($errors)->values()->collapse()->implode(' ');

        throw new HttpResponseException(
            response()->error($message_error, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
