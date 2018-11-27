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
            'name' => 'Lektion 3.1',
            'locale' => 'sv',
            'description' => 'Beskrivning av lektion 3.1'
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => 'Lesson 3.1',
            'locale' => 'en',
            'description' => 'Description of lesson 3.1'
        ]);
        DB::table('lesson_track')->insert([
            'track_id' => 3,
            'lesson_id' => $id
        ]);

        $id = DB::table('lessons')->insertGetId([
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => 'Lektion 3.2',
            'locale' => 'sv'
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => 'Lesson 3.2',
            'locale' => 'en'
        ]);
        DB::table('lesson_track')->insert([
            'track_id' => 3,
            'lesson_id' => $id
        ]);

        $id = DB::table('lessons')->insertGetId([
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => 'Lektion 3.3',
            'locale' => 'sv'
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => 'Lesson 3.3',
            'locale' => 'en'
        ]);
        DB::table('lesson_track')->insert([
            'track_id' => 3,
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
