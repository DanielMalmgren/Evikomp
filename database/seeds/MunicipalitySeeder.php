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
            'name' => 'Ödeshög',
            'orgnummer' => '212000-0373'
        ]);
        DB::table('municipalities')->insert([
            'name' => 'Boxholm',
            'orgnummer' => '212000-0407'
        ]);
        DB::table('municipalities')->insert([
            'name' => 'Kinda',
            'orgnummer' => '212000-0399'
        ]);
        DB::table('municipalities')->insert([
            'name' => 'Ydre',
            'orgnummer' => '212000-0381'
        ]);
        DB::table('municipalities')->insert([
            'name' => 'Linköping',
            'orgnummer' => '212000-0449'
        ]);
        DB::table('municipalities')->insert([
            'name' => 'Finspång',
            'orgnummer' => '212000-0423'
        ]);
        DB::table('municipalities')->insert([
            'name' => 'Mjölby',
            'orgnummer' => '212000-0480'
        ]);
        DB::table('municipalities')->insert([
            'name' => 'Motala',
            'orgnummer' => '212000-2817'
        ]);
        DB::table('municipalities')->insert([
            'name' => 'Norrköping',
            'orgnummer' => '212000-0456'
        ]);
        DB::table('municipalities')->insert([
            'name' => 'Söderköping',
            'orgnummer' => '212000-0464'
        ]);
        DB::table('municipalities')->insert([
            'name' => 'Vadstena',
            'orgnummer' => '212000-2825'
        ]);
        DB::table('municipalities')->insert([
            'name' => 'Valdemarsvik',
            'orgnummer' => '212000-0431'
        ]);
        DB::table('municipalities')->insert([
            'name' => 'Åtvidaberg',
            'orgnummer' => '212000-0415'
        ]);
    }
}
