<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->success([], 'ecommerce backend 1.0', Response::HTTP_OK);
});
