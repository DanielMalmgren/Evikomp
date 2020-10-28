<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('colors')->insert([
            'name' => 'white',
            'hex' => '#ffffff',
        ]);
        DB::table('colors')->insert([
            'name' => 'black',
            'hex' => '#000000',
        ]);
        DB::table('colors')->insert([
            'name' => 'red',
            'hex' => '#ff0000',
        ]);
        DB::table('colors')->insert([
            'name' => 'green',
            'hex' => '#00ff00',
        ]);
        DB::table('colors')->insert([
            'name' => 'blue',
            'hex' => '#0000ff',
        ]);
    }
}
