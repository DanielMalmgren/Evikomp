<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracks', function (Blueprint $table) {
            $table->integer('id')->unsigned();
            $table->primary('id');
            //$table->string('name'); //Moved to track_translations to be localized
            $table->timestamps();
        });

        Schema::create('track_translations', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('track_id')->unsigned();
            $table->string('name');
            $table->string('subtitle')->nullable();
            $table->string('locale')->index();

            $table->unique(['track_id','locale']);
            $table->foreign('track_id')->references('id')->on('tracks')->onDelete('cascade');
        });

        Schema::create('track_workplace', function (Blueprint $table) {
            $table->unsignedInteger('track_id');
            $table->unsignedInteger('workplace_id');

            $table->foreign('track_id')
                ->references('id')
                ->on('tracks')
                ->onDelete('cascade');

            $table->foreign('workplace_id')
                ->references('id')
                ->on('workplaces')
                ->onDelete('cascade');

            $table->primary(['track_id', 'workplace_id']);
        });

        Schema::create('track_user', function (Blueprint $table) {
            $table->unsignedInteger('track_id');
            $table->unsignedInteger('user_id');

            $table->foreign('track_id')
                ->references('id')
                ->on('tracks')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->primary(['track_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('track_workplace');
        Schema::dropIfExists('track_user');
        Schema::dropIfExists('track_translations');
        Schema::dropIfExists('tracks');
    }
}
