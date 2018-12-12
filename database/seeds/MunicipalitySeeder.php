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
        DB::table('municipalities')->insert([
            'name' => 'Finspång'
        ]);
        DB::table('municipalities')->insert([
            'name' => 'Mjölby'
        ]);
        DB::table('municipalities')->insert([
            'name' => 'Motala'
        ]);
        DB::table('municipalities')->insert([
            'name' => 'Norrköping'
        ]);
        DB::table('municipalities')->insert([
            'name' => 'Söderköping'
        ]);
        DB::table('municipalities')->insert([
            'name' => 'Vadstena'
        ]);
        DB::table('municipalities')->insert([
            'name' => 'Valdemarsvik'
        ]);
        DB::table('municipalities')->insert([
            'name' => 'Åtvidaberg'
        ]);
    }
}
