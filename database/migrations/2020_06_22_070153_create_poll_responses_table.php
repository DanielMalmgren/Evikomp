<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePollResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poll_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('poll_question_id');
            $table->foreign('poll_question_id')->references('id')->on('poll_questions')->onDelete('cascade');
            $table->unsignedBigInteger('poll_session_id');
            $table->foreign('poll_session_id')->references('id')->on('poll_sessions')->onDelete('cascade');
            $table->string('response');
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
        Schema::dropIfExists('poll_responses');
    }
}
