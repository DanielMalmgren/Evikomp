<?php

use Illuminate\Database\Seeder;

class WorkplaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kindaid = DB::table('municipalities')->where('name', 'Kinda')->first()->id;
        DB::table('workplaces')->insert([
            'municipality_id' => $kindaid,
            'name' => 'Stångågården'
        ]);
        DB::table('workplaces')->insert([
            'municipality_id' => $kindaid,
            'name' => 'Bergdala'
        ]);

        $ydreid = DB::table('municipalities')->where('name', 'Ydre')->first()->id;
        DB::table('workplaces')->insert([
            'municipality_id' => $ydreid,
            'name' => 'Solängen'
        ]);
    }
}
