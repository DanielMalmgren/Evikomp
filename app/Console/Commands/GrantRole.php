<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;

class GrantRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:grantrole {email} {role} {--remove}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grant a role to an user';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user = User::where('email', $this->argument('email'))->get()->first();
        if($this->option('remove')) {
            $this->info('Removing '.$user->name.' from role '.$this->argument('role'));
            $user->removeRole($this->argument('role'));
        } else {
            $this->info('Adding '.$user->name.' to role '.$this->argument('role'));
            $user->assignRole($this->argument('role'));
        }
    }
}
