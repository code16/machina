<?php

namespace Code16\Machina;

use Tymon\JWTAuth\Token;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTManager;
use Tymon\JWTAuth\Exceptions\JWTException;
use Code16\Machina\Exceptions\MachinaJwtException;
use Code16\Machina\Exceptions\MissingTokenException;
use Illuminate\Contracts\Auth\Guard as GuardContract;
use Illuminate\Contracts\Auth\Authenticatable;
use Code16\Machina\Exceptions\NotImplementedException;

class MachinaGuard implements GuardContract
{
    /**
     * @var \Tymon\JWTAuth\JWTManager
     */
    protected $manager;

    protected $clientRepository;

    public function __construct(
        JWTManager $manager,
        ClientRepositoryInterface $clientRepository
    )
    {   
        $this->manager = $manager;
        $this->clientRepository = $clientRepository;
    }

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest()
    {
        throw new NotImplementedException("");
    }


    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|null
     */
    public function id()
    {
        throw new NotImplementedException("");
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        throw new NotImplementedException("");
    }

    /**
     * Set the current user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    public function setUser(Authenticatable $user)
    {
        throw new NotImplementedException("");
    }

    /**
     * Attempt to identify a client, and return a token on success
     * 
     * @param  string $client
     * @param  string $secret
     * @return string|bool
     */
    public function attempt($client, $secret)
    {
        $user = $this->clientRepository->findByCredentials($client, $secret);

        if(! $user) {
            return false;
        }

        return $this->generateClientToken($client);
    }

    /**
     * Generate a token using the user identifier as the subject claim.
     *
     * @param string $clientId
     * @param array $customClaims
     *
     * @return string
     */
    public function generateClientToken($clientId, array $customClaims = [])
    {
        $payload = $this->makePayload($clientId, $customClaims);

        return $this->manager->encode($payload)->get();
    }

    /**
     * Create a Payload instance.
     *
     * @param mixed $subject
     * @param array $customClaims
     *
     * @return \Tymon\JWTAuth\Payload
     */
    protected function makePayload($subject, array $customClaims = [])
    {
        return $this->manager->getPayloadFactory()->make(
            array_merge($customClaims, ['sub' => $subject])
        );
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check()
    {
        return ! is_null($this->user());
    }

    /**
     * Get the user for the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Request  $request
     * @return mixed
     */
    public function user()
    {
        $request = request();

        if (! $token = $this->getTokenFromRequest($request)) {
            throw new MachinaJwtException(new MissingTokenException("Token not provided."));
        }

        try {
            $client = $this->getClientFromToken($token);
        } 
        catch (JWTException $e) {
            throw new MachinaJwtException($e);
        }

        return $client ? $client : null;
    }

    /**
     * Return token from request
     * 
     * @param  Request $request
     * @return string
     */
    protected function getTokenFromRequest(Request $request)
    {
        try {
            $token = $this->parseToken($request);
        } catch (JWTException $e) {
            return false;
        }
        
        return $token;
    }

    /**
     * Parse the token from the request.
     *
     * @param string $query
     *
     * @return \Tymon\JWTAuth\Token
     */
    public function parseToken(Request $request, $method = 'bearer', $header = 'authorization', $query = 'token')
    {
        if (! $token = $this->parseAuthHeader($request, $header, $method)) {
            if (! $token = $request->query($query, false)) {
                throw new JWTException('The token could not be parsed from the request', 400);
            }
        }

        return new Token($token);
    }

    /**
     * Parse token from the authorization header.
     *
     * @param string $header
     * @param string $method
     *
     * @return false|string
     */
    protected function parseAuthHeader(Request $request, $header = 'authorization', $method = 'bearer')
    {
        $header = $request->headers->get($header);
        
        if (! starts_with(strtolower($header), $method)) {
            return false;
        }

        return trim(str_ireplace($method, '', $header));
    }

    /**
     * Authorize a client via a token.
     *
     * @param mixed $token
     *
     * @return mixed
     */
    public function getClientFromToken($token)
    {
        $id = $this->getPayload($token)->get('sub');

        if (! $client = $this->clientRepository->find($id)) {
            return false;
        }

        return $client;
    }
    
    /**
     * Refresh current token & send it back
     * 
     * @return string
     */
    public function refresh()
    {
        $token = $this->getTokenFromRequest(request());

        return $this->manager->refresh($token)->get();
    }

    /**
     * Get the raw Payload instance.
     *
     * @param mixed $token
     *
     * @return \Tymon\JWTAuth\Payload
     */
    protected function getPayload($token)
    {
        return $this->manager->decode($token);
    }

    /**
     * Return configured TTL
     * 
     * @return integer
     */
    public function getTTL()
    {
        return $this->manager->getPayloadFactory()->getTTL();
    }

}
