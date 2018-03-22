<?php

namespace Code16\Machina\Tests\Feature;

use Code16\Machina\Tests\MachinaTestCase;
use Code16\Machina\Middleware\RefreshToken;

class RefreshTest extends MachinaTestCase
{

    /** @test */
    function we_can_refresh_the_token_using_middleware()
    {
        $this->app->make('router')->get('protected', function() {
            return response()->json('OK');
        })->middleware(['auth:machina', RefreshToken::class]);

        $client = $this->createClient("1234");
        $data = [
            'client' => $client->id,
            'secret' => "1234",
        ];
        $response = $this->json('post', '/auth/login', $data);
        $response->assertStatus(200);
        $token = $response->decodeResponseJson()['access_token'];

        $response = $this->json('get', '/protected?token='.$token);
        $response->assertStatus(200);
        $refreshedToken = substr($response->headers->all()['authorization'][0], 7);
        $this->assertNotEquals($token, $refreshedToken);

        $response = $this->json('get', '/protected?token='.$token);
        $response->assertStatus(401);

        $response = $this->json('get', '/protected?token='.$refreshedToken);
        $response->assertStatus(200);
    }   
}
