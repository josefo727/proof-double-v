<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\JsonResponse;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        Response::macro('success', function ($response, $message = 'Datos obtenidos exitosamente', int $status = 200) {
            $data = [
                'data' => $response,
                'message' => $message,
                'status' => $status
            ];
            return new JsonResponse($data, $status);
        });

        Response::macro('error', function ($message, $status) {
            $data = [
                'error' => [
                    'message' => $message,
                    'status' => $status
                ]
            ];
            return new JsonResponse($data, $status);
        });
    }
}
