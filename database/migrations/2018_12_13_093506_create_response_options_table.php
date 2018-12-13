<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResponseOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('response_options', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->unsignedInteger('question_id');
            $table->foreign('question_id')->references('id')->on('questions');
            $table->boolean('isCorrectAnswer');
        });

        Schema::create('response_option_translations', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('response_option_id')->unsigned();
            $table->string('text');
            $table->string('locale')->index();

            $table->unique(['response_option_id','locale']);
            $table->foreign('response_option_id')->references('id')->on('response_options')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('response_option_translations');
        Schema::dropIfExists('response_options');
    }
}
