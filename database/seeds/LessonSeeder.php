<?php

use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $id = DB::table('lessons')->insertGetId([
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => '1.1 - Anställningen',
            'locale' => 'sv',
            'description' => 'Om verksamhetens rutiner och förväntningar. Din roll som medarbetare är viktig!'
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => '1.1 - The employment',
            'locale' => 'en',
            'description' => 'About the business\'s routines and expectations. Your role as an employee is important!'
        ]);
        DB::table('lesson_track')->insert([
            'track_id' => 1,
            'lesson_id' => $id
        ]);

        $id = DB::table('lessons')->insertGetId([
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => '1.2 - Bemötande',
            'locale' => 'sv'
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => '1.2 - Treatment',
            'locale' => 'en'
        ]);
        DB::table('lesson_track')->insert([
            'track_id' => 1,
            'lesson_id' => $id
        ]);

        $id = DB::table('lessons')->insertGetId([
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => '1.3 - Dokumentation och sekretess',
            'locale' => 'sv'
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => '1.3 - Documentation and secrecy',
            'locale' => 'en'
        ]);
        DB::table('lesson_track')->insert([
            'track_id' => 1,
            'lesson_id' => $id
        ]);

        $id = DB::table('lessons')->insertGetId([
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => '1.4 - Omvårdnadsarbete',
            'locale' => 'sv'
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => '1.4 - Nursing',
            'locale' => 'en'
        ]);
        DB::table('lesson_track')->insert([
            'track_id' => 1,
            'lesson_id' => $id
        ]);

        $id = DB::table('lessons')->insertGetId([
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => '1.5 - Måltider och andra aktiviteter',
            'locale' => 'sv'
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => '1.5 - Meals and other activities',
            'locale' => 'en'
        ]);
        DB::table('lesson_track')->insert([
            'track_id' => 1,
            'lesson_id' => $id
        ]);

        $id = DB::table('lessons')->insertGetId([
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => '1.6 - Ergonomi och förflyttningsteknik',
            'locale' => 'sv'
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => '1.6 - Ergonomics and transfer techniques',
            'locale' => 'en'
        ]);
        DB::table('lesson_track')->insert([
            'track_id' => 1,
            'lesson_id' => $id
        ]);

        $id = DB::table('lessons')->insertGetId([
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => '1.7 - Palliativ vård',
            'locale' => 'sv'
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => '1.7 - Palliative care',
            'locale' => 'en'
        ]);
        DB::table('lesson_track')->insert([
            'track_id' => 1,
            'lesson_id' => $id
        ]);

        $id = DB::table('lessons')->insertGetId([
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => 'Lektion 4.1',
            'locale' => 'sv'
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => 'Lesson 4.1',
            'locale' => 'en'
        ]);
        DB::table('lesson_track')->insert([
            'track_id' => 4,
            'lesson_id' => $id
        ]);

        $id = DB::table('lessons')->insertGetId([
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => 'Lektion 4.2',
            'locale' => 'sv'
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => 'Lesson 4.2',
            'locale' => 'en'
        ]);
        DB::table('lesson_track')->insert([
            'track_id' => 4,
            'lesson_id' => $id
        ]);
    }
}
