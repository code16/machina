<?php

namespace Code16\Machina\Tests\Stubs;

use Code16\Machina\ClientRepositoryInterface;

class TestRepository implements ClientRepositoryInterface
{
    public function find($id)
    {
        return Client::find($id);
    }

    public function findByCredentials($client, $secret)
    {
        return Client::where('id', $client)->where('secret', $secret)->first();
    }

}