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
            'name' => 'Spår 1 - Introduktion äldreomsorg',
            'locale' => 'sv'
        ]);
        DB::table('track_translations')->insert([
            'track_id' => 1,
            'name' => 'Track 1 - Introduction elder care',
            'locale' => 'en'
        ]);

        DB::table('tracks')->insert([
            'id' => 2,
        ]);
        DB::table('track_translations')->insert([
            'track_id' => 2,
            'name' => 'Spår 2 - Vårdens grunder och teori',
            'locale' => 'sv'
        ]);
        DB::table('track_translations')->insert([
            'track_id' => 2,
            'name' => 'Track 2 - The bases and theory of care',
            'locale' => 'en'
        ]);

        DB::table('tracks')->insert([
            'id' => 3,
        ]);
        DB::table('track_translations')->insert([
            'track_id' => 3,
            'name' => 'Spår 3 - Vara människa',
            'locale' => 'sv'
        ]);
        DB::table('track_translations')->insert([
            'track_id' => 3,
            'name' => 'Track 3 - Being human',
            'locale' => 'en'
        ]);

        DB::table('tracks')->insert([
            'id' => 4,
        ]);
        DB::table('track_translations')->insert([
            'track_id' => 4,
            'name' => 'Spår 4 - Diskriminering',
            'locale' => 'sv'
        ]);
        DB::table('track_translations')->insert([
            'track_id' => 4,
            'name' => 'Track 4 - Discrimination',
            'locale' => 'en'
        ]);

        DB::table('tracks')->insert([
            'id' => 5,
        ]);
        DB::table('track_translations')->insert([
            'track_id' => 5,
            'name' => 'Spår 5 - Äldres hälsa och livskvalitet',
            'locale' => 'sv'
        ]);
        DB::table('track_translations')->insert([
            'track_id' => 5,
            'name' => 'Track 5 - Health and quality of life for elderly',
            'locale' => 'en'
        ]);

        DB::table('tracks')->insert([
            'id' => 6,
        ]);
        DB::table('track_translations')->insert([
            'track_id' => 6,
            'name' => 'Spår 6 - Funktionsnedsättning (LSS)',
            'locale' => 'sv'
        ]);
        DB::table('track_translations')->insert([
            'track_id' => 6,
            'name' => 'Track 6 - Disabilities (LSS)',
            'locale' => 'en'
        ]);

        DB::table('tracks')->insert([
            'id' => 7,
        ]);
        DB::table('track_translations')->insert([
            'track_id' => 7,
            'name' => 'Spår 7 - Demens',
            'locale' => 'sv'
        ]);
        DB::table('track_translations')->insert([
            'track_id' => 7,
            'name' => 'Track 7 - Dementia',
            'locale' => 'en'
        ]);

        DB::table('tracks')->insert([
            'id' => 8,
        ]);
        DB::table('track_translations')->insert([
            'track_id' => 8,
            'name' => 'Spår 8',
            'locale' => 'sv'
        ]);
        DB::table('track_translations')->insert([
            'track_id' => 8,
            'name' => 'Track 8',
            'locale' => 'en'
        ]);
    }
}
