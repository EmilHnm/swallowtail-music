<?php

namespace App\Console\Commands\System;

use App\Models\User;
use Illuminate\Console\Command;

class AdminAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:admin-account {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        User::where('email', $this->argument('email'))->update([
            'user_id' => \Str::uuid(),
            'email_verified_at' => now(),
            'role' => 'Admin',
        ]);
    }
}
