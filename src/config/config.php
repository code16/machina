<?php 

return [
    
    /*
    |--------------------------------------------------------------------------
    | Authentication route prefix
    |--------------------------------------------------------------------------
    | Defines the route prefix that will be use by clients to retrieve/refresh
    | JWT token from the application.
    |
    */
    'route-prefix' => 'api/auth',

    /*
    |--------------------------------------------------------------------------
    | Client identifier
    |--------------------------------------------------------------------------
    | Identifier parameter name that will be used to identity client that request
    | a token.
    |
    */
    'client-parameter' => 'client',

    /*
    |--------------------------------------------------------------------------
    | Secret identitifier
    |--------------------------------------------------------------------------
    | Define the parameter name that will be used to authenticate the client on
    | a token request.
    |
    */
    'secret-parameter' => 'secret',

];
