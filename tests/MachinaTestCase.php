<?php

namespace Code16\Machina\Tests;

use Code16\Machina\MachinaServiceProvider;
use Code16\Machina\Tests\Stubs\Client;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class MachinaTestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('jwt:secret');
        Schema::create('clients', function($table) {
            $table->increments('id');
            $table->string('secret');
            $table->timestamps();
        });
    }

    protected function getEnvironmentSetUp($app)
    {       
        $app['config']->set('app.key', Str::random(32));
        $app['config']->set('database.default', "sqlite");
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        $app['config']->set('auth.guards.machina', [
            'driver' => 'machina',
        ]);
        $app['config']->set('machina.route-prefix', "auth");

        $app->bind(
            \Code16\Machina\ClientRepositoryInterface::class,
            \Code16\Machina\Tests\Stubs\TestRepository::class
        );
    }

    protected function getPackageProviders($app)
    {
        return [MachinaServiceProvider::class];
    }

    /**
     * Create and return test client
     * 
     * @param  string|null $secret
     * @return Client
     */
    protected function createClient(string $secret = null) : Client
    {
        $client = new Client;
        $client->secret = $secret ? $secret : Str::random(32);
        $client->save();
        return $client;
    }
}
