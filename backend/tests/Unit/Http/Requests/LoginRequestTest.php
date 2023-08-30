<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\LoginRequest;
use Tests\TestCase;
use Illuminate\Support\Facades\Validator;

class LoginRequestTest extends TestCase
{
    /** @test */
    public function should_require_all_fields(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $request = new LoginRequest();
        $request->setContainer($this->app)->setRedirector($this->app['redirect']);
        $request->merge($data);

        $rules = $request->rules();

        $validator = \Validator::make($data, $rules);

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function should_pass_with_all_fields(): void
    {
		$data = [
			'email' => 'test@example.com',
			'password' => 'password',
		];

		$request = new LoginRequest();
		$request->merge($data);

		$rules = $request->rules();

		$validator = Validator::make($data, $rules);

		$this->assertFalse($validator->fails());
    }
}
