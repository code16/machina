<?php

namespace Code16\Machina\Tests\Feature;

use Code16\Machina\Middleware\RefreshToken;
use Code16\Machina\Tests\MachinaTestCase;

class RefreshTest extends MachinaTestCase
{

    /** @test */
    function we_can_refresh_the_token_using_middleware()
    {
        $this->app
            ->make('router')
            ->get('protected', function() {
                return response()->json('OK');
            })
            ->middleware(['auth:machina', RefreshToken::class]);

        $client = $this->createClient("1234");
        $token = $this
            ->postJson('/auth/login', [
                'client' => $client->id,
                'secret' => "1234",
            ])
            ->assertStatus(200)
            ->decodeResponseJson()['access_token'];

        $response = $this
            ->getJson("/protected?token=$token")
            ->assertStatus(200);
        
        $refreshedToken = substr($response->headers->all()['authorization'][0], 7);
        $this->assertNotEquals($token, $refreshedToken);

        $this->getJson("/protected?token=$token")
            ->assertStatus(401);

        $this->getJson("/protected?token=$refreshedToken")
            ->assertStatus(200);
    }
}
