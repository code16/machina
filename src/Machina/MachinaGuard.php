<?php

namespace Code16\Machina;

use Code16\Machina\Exceptions\MachinaJwtException;
use Code16\Machina\Exceptions\MissingTokenException;
use Code16\Machina\Exceptions\NotImplementedException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard as GuardContract;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Exceptions\InvalidClaimException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\PayloadException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenBlacklistedException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Manager;
use PHPOpenSourceSaver\JWTAuth\Token;

class MachinaGuard implements GuardContract
{
    public function __construct(protected Manager $manager, protected ClientRepositoryInterface $clientRepository)
    {}

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
     * @return \PHPOpenSourceSaver\JWTAuth\Payload
     */
    protected function makePayload($subject, array $customClaims = [])
    {
        $claims = array_merge($customClaims, ['sub' => $subject]);

        return $this->manager->getPayloadFactory()->customClaims($claims)->make();
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     * @throws MachinaJwtException
     */
    public function check()
    {
        return ! is_null($this->user());
    }

    /**
     * Determine if the guard has a user instance.
     *
     * @return bool
     */
    public function hasUser()
    {
        return ! is_null($this->user());
    }

    /**
     * Get the user for the incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     * @throws MachinaJwtException
     */
    public function user()
    {
        $request = request();

        if (! $token = $this->getTokenFromRequest($request)) {
            throw new MachinaJwtException(new MissingTokenException("Token not provided."), 400);
        }

        try {
            $client = $this->getClientFromToken($token);
        } 
        catch (TokenBlacklistedException|TokenExpiredException $e) {
            throw new MachinaJwtException($e, 401);
        }
        catch (TokenInvalidException|PayloadException|InvalidClaimException $e) {
            throw new MachinaJwtException($e, 400);
        }

        return $client ?? null;
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
        } catch (JWTException) {
            return false;
        }
        
        return $token;
    }

    /**
     * Parse the token from the request.
     *
     * @param string $query
     *
     * @return \PHPOpenSourceSaver\JWTAuth\Token
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
        
        if (! Str::startsWith(strtolower($header), $method)) {
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

        if (! $client = $this->clientRepository->findByKey($id)) {
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

        return $this->manager->refresh($token, true)->get();
    }

    /**
     * Get the raw Payload instance.
     *
     * @param mixed $token
     *
     * @return \PHPOpenSourceSaver\JWTAuth\Payload
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
