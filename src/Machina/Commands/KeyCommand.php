<?php

namespace Code16\Machina\Commands;

use Illuminate\Console\Command;

class KeyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'machina:keys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Key used to create JWT tokens';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('jwt:secret');
    }
}
