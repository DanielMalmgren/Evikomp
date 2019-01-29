<?php

use Illuminate\Database\Seeder;

class TitleSeeder extends Seeder
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

        DB::table('titles')->insert([
            'name' => 'Undersköterska',
            'workplace_type_id' => $aoid
        ]);
        DB::table('titles')->insert([
            'name' => 'Vårdbiträde',
            'workplace_type_id' => $aoid
        ]);
        DB::table('titles')->insert([
            'name' => 'Sjuksköterska',
            'workplace_type_id' => $aoid
        ]);
        DB::table('titles')->insert([
            'name' => 'Chef',
            'workplace_type_id' => $aoid
        ]);
        DB::table('titles')->insert([
            'name' => 'Annat',
            'workplace_type_id' => $aoid
        ]);

        DB::table('titles')->insert([
            'name' => 'Chef',
            'workplace_type_id' => $lssid
        ]);
        DB::table('titles')->insert([
            'name' => 'Sjuksköterska',
            'workplace_type_id' => $lssid
        ]);
        DB::table('titles')->insert([
            'name' => 'Annat',
            'workplace_type_id' => $lssid
        ]);
    }
}
