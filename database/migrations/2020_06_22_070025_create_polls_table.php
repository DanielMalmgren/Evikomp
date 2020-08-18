<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->date('active_from')->nullable();
            $table->date('active_to')->nullable();
            $table->unsignedTinyInteger('scope_terms_of_employment')->default(0);
            $table->unsignedTinyInteger('scope_full_or_part_time')->default(0);
            $table->timestamps();
        });

        Schema::create('poll_translations', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedBigInteger('poll_id')->unsigned();
            $table->string('name', 100);
            $table->string('infotext')->nullable();
            $table->string('infotext2')->nullable();
            $table->string('locale')->index();

            $table->unique(['poll_id','locale']);
            $table->foreign('poll_id')->references('id')->on('polls')->onDelete('cascade');
        });

        Schema::create('poll_workplace', function (Blueprint $table) {
            $table->unsignedBigInteger('poll_id');
            $table->unsignedInteger('workplace_id');

            $table->foreign('poll_id')
                ->references('id')
                ->on('polls')
                ->onDelete('cascade');

            $table->foreign('workplace_id')
                ->references('id')
                ->on('workplaces')
                ->onDelete('cascade');

            $table->primary(['poll_id', 'workplace_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('poll_workplace');
        Schema::dropIfExists('poll_municipality');
        Schema::dropIfExists('poll_translations');
        Schema::dropIfExists('polls');
    }
}
