<?php

namespace Code16\Machina;

interface ClientRepositoryInterface
{
    /**
     * Retrieve an user by its key
     * 
     * @param  string $key
     * @return object|null
     */
    public function findByKey($key);

    /**
     * Retrieve an user by its credentials
     * 
     * @param  string $client
     * @param  string $secret
     * @return object|null
     */
    public function findByCredentials($client, $secret);
}
