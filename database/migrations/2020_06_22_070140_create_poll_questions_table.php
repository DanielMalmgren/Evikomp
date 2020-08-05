<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePollQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poll_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('poll_id');
            $table->foreign('poll_id')->references('id')->on('polls')->onDelete('cascade');
            $table->string('type', 20);
            $table->unsignedTinyInteger('min_alternatives')->default(0);
            $table->unsignedTinyInteger('max_alternatives')->default(0);
            $table->boolean('compulsory')->default(false);
            $table->unsignedTinyInteger('order')->default(0);
            $table->string('display_criteria')->default('');
            $table->timestamps();
        });

        Schema::create('poll_question_translations', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedBigInteger('poll_question_id')->unsigned();
            $table->string('text');
            $table->string('alternatives')->nullable();
            $table->string('locale')->index();

            $table->unique(['poll_question_id','locale']);
            $table->foreign('poll_question_id')->references('id')->on('poll_questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('poll_question_translations');
        Schema::dropIfExists('poll_questions');
    }
}
