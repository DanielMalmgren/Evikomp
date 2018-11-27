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

        $this->call(WorkplaceSeeder::class);

        $this->call(TrackSeeder::class);

        $this->call(LessonSeeder::class);

        $this->call(LocaleSeeder::class);

        $this->call(RolesAndPermissionsSeeder::class);

        factory(App\User::class, 10)->create();
    }
}
