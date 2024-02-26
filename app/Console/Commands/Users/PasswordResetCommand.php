<?php

namespace App\Console\Commands\Users;

use Illuminate\Console\Command;

class PasswordResetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:password:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset a user\'s password.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
    }
}
