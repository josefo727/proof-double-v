<?php

namespace Tests\Unit\Providers;

use Tests\TestCase;
use Illuminate\Support\Facades\Response;

class ResponseMacroServiceProviderTest extends TestCase
{
    /** @test */
    public function should_return_success_response(): void
    {
        $response = Response::success(['key' => 'value'], 'Successful operation', 200);

        $this->assertEquals(200, $response->status());
        $this->assertEquals([
            'data' => ['key' => 'value'],
            'message' => 'Successful operation',
            'status' => 200
        ], $response->getData(true));
    }

    /** @test */
    public function should_return_error_response(): void
    {
        $response = Response::error('Something went wrong', 500);

        $this->assertEquals(500, $response->status());
        $this->assertEquals([
            'error' => [
                'message' => 'Something went wrong',
                'status' => 500
            ]
        ], $response->getData(true));
    }
}
