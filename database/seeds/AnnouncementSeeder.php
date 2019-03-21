<?php

use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('announcements')->insert([
            'heading' => 'Spår 3',
            'preamble' => 'Nu är arbetet med spår 3 färdigt, spåret kommer att lanseras under den kommande veckan',
            'created_at' => '2019-01-10'
        ]);

        DB::table('announcements')->insert([
            'heading' => 'Somaliska',
            'preamble' => 'Samtliga lektioner är nu översatta till somaliska.',
            'created_at' => '2019-01-12'
        ]);

        DB::table('announcements')->insert([
            'heading' => 'Kick-off',
            'preamble' => 'Datum är nu spikat för kick-off för samtliga deltagare i projektet!',
            'created_at' => '2019-01-17'
        ]);
    }
}
