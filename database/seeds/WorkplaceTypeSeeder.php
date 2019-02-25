<?php

use Illuminate\Database\Seeder;

class WorkplaceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('workplace_types')->insert([
            'name' => 'Äldreomsorg'
        ]);

        DB::table('workplace_types')->insert([
            'name' => 'LSS'
        ]);

        DB::table('workplace_types')->insert([
            'name' => 'ESF-projektgrupp'
        ]);
    }
}
