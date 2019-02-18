<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->increments('id');
            //$table->string('name'); //Moved to lesson_translations to be localized
            $table->boolean('active')->default(true);
            $table->boolean('limited_by_title')->default(false);
            $table->unsignedInteger('times_started')->default(0);
            $table->unsignedInteger('times_test_started')->default(0);
            $table->unsignedInteger('times_finished')->default(0);
            $table->string('video_id', 10);
            $table->timestamps();
        });

        Schema::create('lesson_translations', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('lesson_id')->unsigned();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('locale')->index();

            $table->unique(['lesson_id','locale']);
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
        });

        Schema::create('lesson_track', function (Blueprint $table) {
            $table->unsignedInteger('track_id');
            $table->unsignedInteger('lesson_id');

            $table->foreign('track_id')
                ->references('id')
                ->on('tracks')
                ->onDelete('cascade');

            $table->foreign('lesson_id')
                ->references('id')
                ->on('lessons')
                ->onDelete('cascade');

            $table->primary(['track_id', 'lesson_id']);
        });

        Schema::create('lesson_title', function (Blueprint $table) {
            $table->unsignedInteger('title_id');
            $table->unsignedInteger('lesson_id');

            $table->foreign('title_id')
                ->references('id')
                ->on('titles')
                ->onDelete('cascade');

            $table->foreign('lesson_id')
                ->references('id')
                ->on('lessons')
                ->onDelete('cascade');

            $table->primary(['title_id', 'lesson_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lesson_track');
        Schema::dropIfExists('lesson_title');
        Schema::dropIfExists('lesson_translations');
        Schema::dropIfExists('lessons');
    }
}
