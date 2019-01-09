<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_responses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('test_session_id');
            $table->foreign('test_session_id')->references('id')->on('test_sessions');
            $table->unsignedInteger('question_id');
            $table->foreign('question_id')->references('id')->on('questions');
            $table->unsignedTinyInteger('wrong_responses')->default(0);
            $table->boolean('correct')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test_responses');
    }
}
