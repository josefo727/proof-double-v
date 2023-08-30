<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Http\Response;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            return response()->error('Registro no encontrado', Response::HTTP_NOT_FOUND);
        }

        if ($request->is('api/*')) {
            $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;
            $code = $exception->getCode();

            $message = match ($code) {
                Response::HTTP_BAD_REQUEST => 'Solicitud incorrecta',
                Response::HTTP_UNAUTHORIZED => 'No autorizado',
                Response::HTTP_FORBIDDEN => 'Prohibido',
                Response::HTTP_NOT_FOUND => 'No encontrado',
                Response::HTTP_METHOD_NOT_ALLOWED => 'Método no permitido',
                Response::HTTP_NOT_ACCEPTABLE => 'No aceptable',
                Response::HTTP_PROXY_AUTHENTICATION_REQUIRED => 'Se requiere autenticación de proxy',
                Response::HTTP_REQUEST_TIMEOUT => 'Tiempo de solicitud agotado',
                Response::HTTP_CONFLICT => 'Conflicto',
                Response::HTTP_GONE => 'Desaparecido',
                Response::HTTP_LENGTH_REQUIRED => 'Longitud requerida',
                Response::HTTP_PRECONDITION_FAILED => 'Fallo de precondición',
                Response::HTTP_REQUEST_ENTITY_TOO_LARGE => 'Entidad de solicitud demasiado grande',
                Response::HTTP_REQUEST_URI_TOO_LONG => 'URI de solicitud demasiado largo',
                Response::HTTP_UNSUPPORTED_MEDIA_TYPE => 'Tipo de medio no soportado',
                Response::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE => 'Rango solicitado no satisfactorio',
                Response::HTTP_EXPECTATION_FAILED => 'Fallo de expectativa',
                Response::HTTP_INTERNAL_SERVER_ERROR => 'Error interno del servidor',
                Response::HTTP_NOT_IMPLEMENTED => 'No implementado',
                Response::HTTP_BAD_GATEWAY => 'Puerta de enlace incorrecta',
                Response::HTTP_SERVICE_UNAVAILABLE => 'Servicio no disponible',
                Response::HTTP_GATEWAY_TIMEOUT => 'Tiempo de espera de la puerta de enlace agotado',
                Response::HTTP_VERSION_NOT_SUPPORTED => 'Versión no soportada',
                default => 'Ocurrió un error en el servidor',
            };

            return response()->json([
                'error' => $message
            ], $statusCode);
        }

        return parent::render($request, $exception);
    }
}
