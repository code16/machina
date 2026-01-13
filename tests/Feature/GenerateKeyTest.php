<?php

namespace Code16\Machina\Tests\Feature;

use Artisan;
use Code16\Machina\Tests\MachinaTestCase;
use PHPUnit\Framework\Attributes\Test;

class GenerateKeyTest extends MachinaTestCase
{

    #[Test]
    function we_can_call_the_artisan_command_to_generate_keys()
    {
        Artisan::call('machina:keys');
        $this->assertTrue(true);
    }   
}
