<?php

use Illuminate\Database\Seeder;

class TrackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tracks')->insert([
            'id' => 1,
        ]);
        DB::table('track_translations')->insert([
            'track_id' => 1,
            'name' => 'Spår 1',
            'locale' => 'sv'
        ]);
        DB::table('track_translations')->insert([
            'track_id' => 1,
            'name' => 'Track 1',
            'locale' => 'en'
        ]);

        DB::table('tracks')->insert([
            'id' => 2,
        ]);
        DB::table('track_translations')->insert([
            'track_id' => 2,
            'name' => 'Spår 2',
            'locale' => 'sv'
        ]);
        DB::table('track_translations')->insert([
            'track_id' => 2,
            'name' => 'Track 2',
            'locale' => 'en'
        ]);

        DB::table('tracks')->insert([
            'id' => 3,
        ]);
        DB::table('track_translations')->insert([
            'track_id' => 3,
            'name' => 'Spår 3',
            'locale' => 'sv'
        ]);
        DB::table('track_translations')->insert([
            'track_id' => 3,
            'name' => 'Track 3',
            'locale' => 'en'
        ]);

        DB::table('tracks')->insert([
            'id' => 4,
        ]);
        DB::table('track_translations')->insert([
            'track_id' => 4,
            'name' => 'Spår 4',
            'locale' => 'sv'
        ]);
        DB::table('track_translations')->insert([
            'track_id' => 4,
            'name' => 'Track 4',
            'locale' => 'en'
        ]);
    }
}
