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
    protected $signature = 'custom:workplaceadmin
                            {email : Mail address, used to identify the correct user}
                            {workplace : Name of the workplace}
                            {attestlevel=2 : Attest level. Can be 2 (default) for coordinator or 3 for boss}
                            {--remove : Remove the user as workplaceadmin for this workplace}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make an user workplaceadmin';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user = User::where('email', $this->argument('email'))->first();
        $workplace = Workplace::where('name', $this->argument('workplace'))->first();
        if($this->option('remove')) {
            $user->admin_workplaces()->detach($workplace);
            if($user->admin_workplaces()->count() === 0) {
                $this->info('Removing '.$user->name.' from role Arbetsplatsadministratör');
                $user->removeRole('Arbetsplatsadministratör');
            }
        } else {
            $user->admin_workplaces()->attach($workplace, ['attestlevel'=>$this->argument('attestlevel')]);
            $this->info('Adding '.$user->name.' to role Arbetsplatsadministratör');
            $user->assignRole('Arbetsplatsadministratör');
        }
    }
}
