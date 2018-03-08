<?php

namespace Code16\Machina\Adapters;

use Tymon\JWTAuth\Providers\User\UserInterface;
use Code16\Machina\Repositories\ClientRepositoryInterface;

class JwtUserAdapter implements UserInterface
{
    /**
     * @var \Code16\Machina\Repositories\ClientRepositoryInterface
     */
    protected $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    /**
     * Get the user by the given key, value.
     *
     * @param string $key
     * @param mixed $value
     * @return Object|null
     */
    public function getBy($key, $value)
    {
        //
    }

}