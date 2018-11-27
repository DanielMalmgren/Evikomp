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
            'id' => 'sv',
            'default' => true
        ]);
        DB::table('locales')->insert([
            'name' => 'english',
            'id' => 'en',
            'default' => false
        ]);
        DB::table('locales')->insert([
            'name' => 'العربية',
            'id' => 'ar',
            'default' => false
        ]);
        DB::table('locales')->insert([
            'name' => 'français',
            'id' => 'fr',
            'default' => false
        ]);
        DB::table('locales')->insert([
            'name' => 'español',
            'id' => 'es',
            'default' => false
        ]);
        DB::table('locales')->insert([
            'name' => 'ትግርኛ',
            'id' => 'ti',
            'default' => false
        ]);
        DB::table('locales')->insert([
            'name' => 'فارسی',
            'id' => 'fa',
            'default' => false
        ]);
        DB::table('locales')->insert([
            'name' => 'soomaali',
            'id' => 'so',
            'default' => false
        ]);
    }
}
