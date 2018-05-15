# Machina

This package is a wrapper around `tymons\jwt-auth`, aimed at providing a simple & flexible machine-to-machine authentication for Laravel 5.5+. 

## Installation

```
    composer require code16/machina
```

## Configuration

If you want to customize some default options like the prefix used for `/login` and `/refresh` endpoints by the package, you can publish it to your application folder : 

```
    php artisan config:publish code16/machina
```

### Using Machina as default api driver in laravel

If you want to use the `machina` guard on every `api` routes from your application, change the `api` guard driver in `config/auth.php` : 

```php
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'machina',
            'provider' => App\ClientRepository::class,
        ],
    ],
```

### Creating a `ClientRepository` class 

This package doesn't come with an opinationated way of retrieving clients, but instead provides a very simple way to adapt it to your application, by providing a class implementing `Code16\Machina\ClientRepositoryInterface`. 

Example :

```php

    namespace App;

    use Code16\Machina\ClientRepositoryInterface;

    class ClientRepository implements ClientRepositoryInterface
    {
        public function find($id)
        {
            return User::find($id);
        }

        public function findByCredentials($client, $secret)
        {
            return User::where('id', $client)->where('secret', $secret)->first();
        }

    }

```

Note that here we used the standard `App\User` model DB to identify our client, but you can use whichever model / fields you like. 

## Protecting routes

```
    Route::get('protected', 'ApiController@index')->middleware('auth:machina');
```

## Authenticating and retriving token

Send a POST request the `/auth/login` endpoint with `client` and `secret` as parameters : 

```
    {
        client : "1",
        secret : "x7jfajleug64hggi"
    }
```

If the credentials are correct, the API will return a JWT token that can be used to access protected routes. 

## Accessing protected routes

There is two ways of passing the token along the request : 

- Passing the token in the `authorization` header with the following string format : `Bearer <token>`

- Passing the token as a query parameter : `https://app.dev/protected?token=<token>`

## Implementing client applications

For your client applications, you can use our companion package, [machina client](https://github.com/code16/machina-client).
