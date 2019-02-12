<?php

use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Vilket kom först, hönan eller ägget?
        $questionid = DB::table('questions')->insertGetId([
            'lesson_id' => 1,
            'order' => 1
        ]);
        DB::table('question_translations')->insert([
            'question_id' => $questionid,
            'text' => 'Vilket kom först, hönan eller ägget?',
            'locale' => 'sv_SE'
        ]);
        DB::table('question_translations')->insert([
            'question_id' => $questionid,
            'text' => 'What came first, the hen or the egg?',
            'locale' => 'en_US'
        ]);

        $responseid = DB::table('response_options')->insertGetId([
            'question_id' => $questionid,
            'isCorrectAnswer' => false
        ]);
        DB::table('response_option_translations')->insert([
            'response_option_id' => $responseid,
            'text' => 'Hönan',
            'locale' => 'sv_SE'
        ]);
        DB::table('response_option_translations')->insert([
            'response_option_id' => $responseid,
            'text' => 'The hen',
            'locale' => 'en_US'
        ]);

        $responseid = DB::table('response_options')->insertGetId([
            'question_id' => $questionid,
            'isCorrectAnswer' => false
        ]);
        DB::table('response_option_translations')->insert([
            'response_option_id' => $responseid,
            'text' => 'Ägget',
            'locale' => 'sv_SE'
        ]);
        DB::table('response_option_translations')->insert([
            'response_option_id' => $responseid,
            'text' => 'The egg',
            'locale' => 'en_US'
        ]);

        $responseid = DB::table('response_options')->insertGetId([
            'question_id' => $questionid,
            'isCorrectAnswer' => true
        ]);
        DB::table('response_option_translations')->insert([
            'response_option_id' => $responseid,
            'text' => 'Tuppen',
            'locale' => 'sv_SE'
        ]);
        DB::table('response_option_translations')->insert([
            'response_option_id' => $responseid,
            'text' => 'The cock',
            'locale' => 'en_US'
        ]);

        //Vilka av följande alternativ är sanna?
        $questionid = DB::table('questions')->insertGetId([
            'lesson_id' => 1,
            'order' => 2,
            'correctAnswers' => 2
        ]);
        DB::table('question_translations')->insert([
            'question_id' => $questionid,
            'text' => 'Vilka av följande kommuner ligger i Östergötland?',
            'locale' => 'sv_SE'
        ]);
        DB::table('question_translations')->insert([
            'question_id' => $questionid,
            'text' => 'Which of the following municipalities are placed in Östergötland?',
            'locale' => 'en_US'
        ]);

        $responseid = DB::table('response_options')->insertGetId([
            'question_id' => $questionid,
            'isCorrectAnswer' => true
        ]);
        DB::table('response_option_translations')->insert([
            'response_option_id' => $responseid,
            'text' => 'Kinda',
            'locale' => 'sv_SE'
        ]);

        $responseid = DB::table('response_options')->insertGetId([
            'question_id' => $questionid,
            'isCorrectAnswer' => false
        ]);
        DB::table('response_option_translations')->insert([
            'response_option_id' => $responseid,
            'text' => 'Östersund',
            'locale' => 'sv_SE'
        ]);

        $responseid = DB::table('response_options')->insertGetId([
            'question_id' => $questionid,
            'isCorrectAnswer' => false
        ]);
        DB::table('response_option_translations')->insert([
            'response_option_id' => $responseid,
            'text' => 'Idre',
            'locale' => 'sv_SE'
        ]);

        $responseid = DB::table('response_options')->insertGetId([
            'question_id' => $questionid,
            'isCorrectAnswer' => true
        ]);
        DB::table('response_option_translations')->insert([
            'response_option_id' => $responseid,
            'text' => 'Ydre',
            'locale' => 'sv_SE'
        ]);
    }
}
