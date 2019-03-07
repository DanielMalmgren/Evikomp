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
            'video_id' => '321859217',
            'track_id' => 1,
            'order' => 1
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => '1.1 - Anställningen',
            'locale' => 'sv_SE',
            'description' => 'Om verksamhetens rutiner och förväntningar. Din roll som medarbetare är viktig!'
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => '1.1 - The employment',
            'locale' => 'en_US',
            'description' => 'About the business\'s routines and expectations. Your role as an employee is important!'
        ]);

        $id = DB::table('lessons')->insertGetId([
            'video_id' => '259554350',
            'track_id' => 1,
            'order' => 2
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => '1.2 - Bemötande',
            'locale' => 'sv_SE'
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => '1.2 - Treatment',
            'locale' => 'en_US'
        ]);

        $id = DB::table('lessons')->insertGetId([
            'video_id' => '259554350',
            'track_id' => 1,
            'order' => 3
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => '1.3 - Dokumentation och sekretess',
            'locale' => 'sv_SE'
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => '1.3 - Documentation and secrecy',
            'locale' => 'en_US'
        ]);

        $id = DB::table('lessons')->insertGetId([
            'video_id' => '259554350',
            'track_id' => 1,
            'order' => 4
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => '1.4 - Omvårdnadsarbete',
            'locale' => 'sv_SE'
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => '1.4 - Nursing',
            'locale' => 'en_US'
        ]);

        $id = DB::table('lessons')->insertGetId([
            'video_id' => '259554350',
            'track_id' => 1,
            'order' => 5
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => '1.5 - Måltider och andra aktiviteter',
            'locale' => 'sv_SE'
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => '1.5 - Meals and other activities',
            'locale' => 'en_US'
        ]);

        $id = DB::table('lessons')->insertGetId([
            'video_id' => '259554350',
            'track_id' => 1,
            'order' => 6
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => '1.6 - Ergonomi och förflyttningsteknik',
            'locale' => 'sv_SE'
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => '1.6 - Ergonomics and transfer techniques',
            'locale' => 'en_US'
        ]);

        $id = DB::table('lessons')->insertGetId([
            'video_id' => '259554350',
            'track_id' => 1,
            'order' => 7
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => '1.7 - Palliativ vård',
            'locale' => 'sv_SE'
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => '1.7 - Palliative care',
            'locale' => 'en_US'
        ]);

        $id = DB::table('lessons')->insertGetId([
            'video_id' => '259554350',
            'track_id' => 4,
            'order' => 1
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => 'Lektion 4.1',
            'locale' => 'sv_SE'
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => 'Lesson 4.1',
            'locale' => 'en_US'
        ]);

        $id = DB::table('lessons')->insertGetId([
            'video_id' => '259554350',
            'track_id' => 4,
            'order' => 2
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => 'Lektion 4.2',
            'locale' => 'sv_SE'
        ]);
        DB::table('lesson_translations')->insert([
            'lesson_id' => $id,
            'name' => 'Lesson 4.2',
            'locale' => 'en_US'
        ]);
    }
}
