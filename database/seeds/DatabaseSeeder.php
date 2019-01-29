<?php

use Illuminate\Database\Seeder;

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

        $this->call(LessonSeeder::class);

        $this->call(QuestionSeeder::class);

        $this->call(LocaleSeeder::class);

        $this->call(RolesAndPermissionsSeeder::class);

        $this->call(AnnouncementSeeder::class);

        if(App::environment('lab') || App::environment('dev')) {
            $this->command->comment('Generating dummy users...');
            factory(App\User::class, 50)->create();
        }
    }
}
