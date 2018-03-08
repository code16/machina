<?php

namespace Code16\Machina\Commands;

use Illuminate\Console\Command;

class CreateClientCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'machina:create-client';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new API client';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
    }
}
