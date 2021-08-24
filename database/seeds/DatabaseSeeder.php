<?php

use Illuminate\Database\Seeder;
use App\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(MunicipalitySeeder::class);

        $this->call(WorkplaceTypeSeeder::class);

        $this->call(TitleSeeder::class);

        $this->call(WorkplaceSeeder::class);

        $this->call(TrackSeeder::class);

        $this->call(LocaleSeeder::class);

        $this->call(RolesAndPermissionsSeeder::class);

        $this->call(ProjectTimeTypeSeeder::class);

        if(App::environment('lab') || App::environment('local')) {
            $this->call(LessonSeeder::class);

            $this->call(QuestionSeeder::class);

            $this->call(AnnouncementSeeder::class);

            $this->command->comment('Generating dummy users...');
            User::factory()->count(250)->create();
        }
    }
}
