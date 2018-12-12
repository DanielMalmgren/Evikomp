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
        //Kinda
        $id = DB::table('municipalities')->where('name', 'Kinda')->first()->id;
        DB::table('workplaces')->insert([
            'municipality_id' => $id,
            'name' => 'Stångågården'
        ]);
        DB::table('workplaces')->insert([
            'municipality_id' => $id,
            'name' => 'Bergdala'
        ]);
        DB::table('workplaces')->insert([
            'municipality_id' => $id,
            'name' => 'Västerliden'
        ]);

        //Ydre
        $id = DB::table('municipalities')->where('name', 'Ydre')->first()->id;
        DB::table('workplaces')->insert([
            'municipality_id' => $id,
            'name' => 'Solängen'
        ]);

        //Boxholm
        $id = DB::table('municipalities')->where('name', 'Boxholm')->first()->id;
        DB::table('workplaces')->insert([
            'municipality_id' => $id,
            'name' => 'Bjursdalen'
        ]);

        //Åtvidaberg
        $id = DB::table('municipalities')->where('name', 'Åtvidaberg')->first()->id;
        DB::table('workplaces')->insert([
            'municipality_id' => $id,
            'name' => 'Rosengården'
        ]);
    }
}
