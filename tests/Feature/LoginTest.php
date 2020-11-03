<?php

namespace Code16\Machina\Tests\Feature;

use Code16\Machina\Tests\MachinaTestCase;

class LoginTest extends MachinaTestCase
{
    /** @test */
    function login_with_valid_credentials_returns_a_jwt_token()
    {
        $client = $this->createClient("1234");
        $data = [
            'client' => $client->id,
            'secret' => "1234",
        ];
        $response = $this->json('post', '/auth/login', $data);
        $response->assertStatus(200);
    }

    /** @test */
    function login_with_invalid_credentials_returns_a_401()
    {
        $client = $this->createClient("1234");
        $data = [
            'client' => $client->id,
            'secret' => "12345",
        ];
        $response = $this->json('post', '/auth/login', $data);
        $response->assertStatus(401);
    }    

    /** @test */
    function login_with_no_credentials_parameters_returns_a_401()
    {
        $client = $this->createClient("1234");
        $data = [
            'client' => $client->id,
        ];
        $response = $this->json('post', '/auth/login', $data);
        $response->assertStatus(401);

        $client = $this->createClient("1234");
        $data = [
           'secret' => "12345",
        ];
        $response = $this->json('post', '/auth/login', $data);
        $response->assertStatus(401);

        $client = $this->createClient("1234");
        $data = [];
        $response = $this->json('post', '/auth/login', $data);
        $response->assertStatus(401);
    }
}
