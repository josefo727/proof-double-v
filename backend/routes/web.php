<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->successResponse([], 'ecommerce backend 1.0', Response::HTTP_OK);
});
