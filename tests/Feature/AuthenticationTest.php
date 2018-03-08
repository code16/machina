<?php

namespace Code16\Machina\Tests\Feature;

use Code16\Machina\Tests\MachinaTestCase;

class AuthenticationTest extends MachinaTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->app->make('router')->get('protected', function() {
            return response()->json('OK');
        })->middleware('auth:machina');
    }

    /** @test */
    public function client_can_access_a_protected_route_with_a_valid_token_in_url()
    {
        $client = $this->createClient("1234");
        $data = [
            'client' => $client->id,
            'secret' => "1234",
        ];
        $response = $this->json('post', '/auth/login', $data);
        $response->assertStatus(200);
        $token = $response->decodeResponseJson()['access_token'];

        $headers = [
            'authorization' => 'Bearer' . $token,
        ];
        $response = $this->withHeaders($headers)->json('get', '/protected');
        $response->assertStatus(200);
    }

    /** @test */
    public function accessing_a_protected_route_without_a_token_returns_400()
    {
        $response = $this->json('get', '/protected');
        $response->assertStatus(400);
    }

    /** @test */
    public function accessing_a_protected_route_with_an_invalid_token_returns_401()
    {
        $headers = [
            'authorization' => 'Bearer1234',
        ];
        $response = $this->withHeaders($headers)->json('get', '/protected');
        $response->assertStatus(400);
    }

    /** @test */
    public function user_is_authenticated_when_a_protected_route_is_accessed()
    {
        $client = $this->createClient("1234");
        $data = [
            'client' => $client->id,
            'secret' => "1234",
        ];
        $response = $this->json('post', '/auth/login', $data);
        $response->assertStatus(200);
        $token = $response->decodeResponseJson()['access_token'];

        $headers = [
            'authorization' => 'Bearer' . $token,
        ];
        $response = $this->withHeaders($headers)->json('get', '/protected');
        $response->assertStatus(200);
        $this->assertNotNull(auth()->user());
        $this->assertEquals($client->id, auth()->user()->id);
    }
}
