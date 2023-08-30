<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        try {
            // Validate input data
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            // User search
            $user = User::query()->where('email', $request->email)->first();

            // Check if the user exists and the password is correct.
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->error('Credenciales no válidas', Response::HTTP_UNAUTHORIZED);
            }

            // Revoke previous tokens
            $user->tokens()->delete();

            // Create a new token
            $token = $user->createToken('app-token')->plainTextToken;

            // Return the token in the response
            return response()->success(['token' => $token], 'Inicio de sesión exitoso', Response::HTTP_OK);

        } catch (\Exception $e) {
            // Catch any exception and return an error response
			$message = __(':class. :method: :message',
				[
					'class' => debug_backtrace()[0]['class'],
					'method'   => debug_backtrace()[0]['function'],
					'message'   => $e->getMessage()
				]
			);
            Log::error($message);
            return response()->error('Error en el servidor', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
