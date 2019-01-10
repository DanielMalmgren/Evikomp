<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Workplace;

class WorkplaceAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:workplaceadmin {email} {workplace} {--remove}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make an user workplaceadmin';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user = User::where('email', $this->argument('email'))->get()->first();
        $workplace = Workplace::where('name', $this->argument('workplace'))->get()->first();
        if($this->option('remove')) {
            $user->admin_workplaces()->detach($workplace);
            if($user->admin_workplaces()->count() == 0) {
                $this->info('Removing '.$user->name.' from role WorkplaceAdmin');
                $user->removeRole('WorkplaceAdmin');
            }
        } else {
            $user->admin_workplaces()->attach($workplace);
            $this->info('Adding '.$user->name.' to role WorkplaceAdmin');
            $user->assignRole('WorkplaceAdmin');
        }
    }
}
