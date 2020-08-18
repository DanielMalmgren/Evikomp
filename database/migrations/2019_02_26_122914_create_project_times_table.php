<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_times', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            $table->time('starttime');
            $table->time('endtime');
            $table->unsignedInteger('workplace_id');
            $table->foreign('workplace_id')->references('id')->on('workplaces');
            $table->unsignedInteger('project_time_type_id');
            $table->foreign('project_time_type_id')->references('id')->on('project_time_types');
            $table->timestamps();
        });

        Schema::create('project_time_user', function (Blueprint $table) {
            $table->unsignedInteger('project_time_id');
            $table->unsignedInteger('user_id');

            $table->foreign('project_time_id')
                ->references('id')
                ->on('project_times')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->primary(['project_time_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_time_user');
        Schema::dropIfExists('project_times');
    }
}
