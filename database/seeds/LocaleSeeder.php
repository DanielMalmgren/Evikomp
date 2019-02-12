<?php

use Illuminate\Database\Seeder;

class LocaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('locales')->insert([
            'name' => 'svenska',
            'id' => 'sv_SE',
            'default' => true
        ]);
        DB::table('locales')->insert([
            'name' => 'english',
            'id' => 'en_US',
            'default' => false
        ]);
        DB::table('locales')->insert([
            'name' => 'العربية',
            'id' => 'ar_AE',
            'default' => false
        ]);
        DB::table('locales')->insert([
            'name' => 'français',
            'id' => 'fr_FR',
            'default' => false
        ]);
        DB::table('locales')->insert([
            'name' => 'español',
            'id' => 'es_ES',
            'default' => false
        ]);
        DB::table('locales')->insert([
            'name' => 'ትግርኛ',
            'id' => 'ti_ER',
            'default' => false
        ]);
        DB::table('locales')->insert([
            'name' => 'فارسی',
            'id' => 'fa_IR',
            'default' => false
        ]);
        DB::table('locales')->insert([
            'name' => 'soomaali',
            'id' => 'so_SO',
            'default' => false
        ]);
    }
}
