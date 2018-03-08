<?php

namespace Code16\Machina;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Code16\Machina\Exceptions\NotImplementedException;
use Code16\Machina\Repositories\ClientRepositoryInterface;

/**
 * Probably not needed. 
 */
class ClientUserProvider implements UserProvider
{
    /**
     * @var ClientRepositoryInterface
     */
    protected $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function retrieveById($identifier)
    {
        return Client::findOrFail($identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function retrieveByToken($identifier, $token)
    {
        throw new NotImplementedException();
    }

    /**
     * {@inheritdoc}
     */
    public function updateRememberToken(Authenticatable $client, $token)
    {
        throw new NotImplementedException();
    }    

    /**
     * {@inheritdoc}
     */
    public function retrieveByCredentials(array $credentials)
    {
        throw new NotImplementedException();
    }

    /**
     * {@inheritdoc}
     */
    public function validateCredentials(Authenticatable $client, array $credentials)
    {
        return true;
    }


}