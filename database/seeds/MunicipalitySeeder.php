<?php

use Illuminate\Database\Seeder;

class MunicipalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('municipalities')->insert([
            'name' => 'Ödeshög'
        ]);
        DB::table('municipalities')->insert([
            'name' => 'Boxholm'
        ]);
        DB::table('municipalities')->insert([
            'name' => 'Kinda'
        ]);
        DB::table('municipalities')->insert([
            'name' => 'Ydre'
        ]);
        DB::table('municipalities')->insert([
            'name' => 'Linköping'
        ]);
    }
}
