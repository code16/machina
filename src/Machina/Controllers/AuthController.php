<?php

namespace Code16\Machina\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Auth\AuthManager;

class AuthController extends Controller
{
    /**
     * @var \Illuminate\Auth\AuthManager
     */
    protected $authManager;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(AuthManager $authManager)
    {
        $this->authManager = $authManager;
    }

    /**
     * Login using client credentials
     *
     * @return \Illuminate\Http\JsonResponses
     */
    public function create(Request $request)
    {
        $client = $request->get(config('machina.client-parameter'));
        $secret = $request->get(config('machina.secret-parameter'));
        
        if (! $token = $this->authManager->guard('machina')->attempt($client, $secret)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->authManager->guard('machina')->getTTL() * 60,
        ]);
    }
}
