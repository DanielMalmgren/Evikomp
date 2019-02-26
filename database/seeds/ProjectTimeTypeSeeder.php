<?php

use Illuminate\Database\Seeder;

class ProjectTimeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('project_time_types')->insert([
            'name' => 'Lektionstid på plats'
        ]);

        DB::table('project_time_types')->insert([
            'name' => 'Egna studier'
        ]);

        DB::table('project_time_types')->insert([
            'name' => 'Förtäring av tårta'
        ]);
    }
}
