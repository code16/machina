<?php

namespace Code16\Machina\Repositories;

interface ClientRepositoryInterface
{
    /**
     * Retrieve an user by its id
     * 
     * @param  string $id 
     * @return object|null
     */
    public function find($id);

    /**
     * Retrieve an user by its credentials
     * 
     * @param  string $client
     * @param  string $secret
     * @return object|null
     */
    public function findByCredentials($client, $secret);
}
