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
        $aoid = DB::table('workplace_types')->where('name', 'Äldreomsorg')->first()->id;
        $lssid = DB::table('workplace_types')->where('name', 'LSS')->first()->id;

        //Kinda
        $id = DB::table('municipalities')->where('name', 'Kinda')->first()->id;
        DB::table('workplaces')->insert([
            'municipality_id' => $id,
            'name' => 'Stångågården',
            'workplace_type_id' => $aoid
        ]);
        DB::table('workplaces')->insert([
            'municipality_id' => $id,
            'name' => 'Bergdala',
            'workplace_type_id' => $aoid
        ]);
        DB::table('workplaces')->insert([
            'municipality_id' => $id,
            'name' => 'Västerliden',
            'workplace_type_id' => $aoid
        ]);

        //Ydre
        $id = DB::table('municipalities')->where('name', 'Ydre')->first()->id;
        DB::table('workplaces')->insert([
            'municipality_id' => $id,
            'name' => 'Solängen',
            'workplace_type_id' => $aoid
        ]);

        //Boxholm
        $id = DB::table('municipalities')->where('name', 'Boxholm')->first()->id;
        DB::table('workplaces')->insert([
            'municipality_id' => $id,
            'name' => 'Bjursdalen',
            'workplace_type_id' => $aoid
        ]);

        //Åtvidaberg
        $id = DB::table('municipalities')->where('name', 'Åtvidaberg')->first()->id;
        DB::table('workplaces')->insert([
            'municipality_id' => $id,
            'name' => 'Rosengården',
            'workplace_type_id' => $aoid
        ]);
    }
}
