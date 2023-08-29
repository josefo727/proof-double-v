<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        try {
            // Validar los datos de entrada
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            // Buscar el usuario
            $user = User::query()->where('email', $request->email)->first();

            // Verificar si el usuario existe y la contraseña es correcta
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->errorResponse('Credenciales no válidas', 401);
            }

            // Revocar tokens anteriores
            $user->tokens()->delete();

            // Crear un nuevo token
            $token = $user->createToken('app-token')->plainTextToken;

            // Devolver el token en la respuesta
            return response()->successResponse(['token' => $token], 'Inicio de sesión exitoso', 200);

        } catch (\Exception $e) {
            // Capturar cualquier excepción y devolver una respuesta de error
            return response()->errorResponse('Error en el servidor', 500);
        }
    }
}
