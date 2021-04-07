<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBookingFieldsToProjectTimes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_times', function (Blueprint $table) {
            $table->boolean('need_teacher')->default(false);
            $table->unsignedInteger('teacher_id')->nullable();
            $table->foreign('teacher_id')->references('id')->on('users');
            $table->unsignedInteger('training_coordinator_id')->nullable();
            $table->foreign('training_coordinator_id')->references('id')->on('workplaces');
        });

        Schema::create('lesson_project_time', function (Blueprint $table) {
            $table->unsignedInteger('project_time_id');
            $table->unsignedInteger('lesson_id');

            $table->foreign('project_time_id')
                ->references('id')
                ->on('project_times')
                ->onDelete('cascade');

            $table->foreign('lesson_id')
                ->references('id')
                ->on('lessons')
                ->onDelete('cascade');

            $table->primary(['project_time_id', 'lesson_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_times', function (Blueprint $table) {
            $table->dropColumn('need_teacher');
            $table->dropColumn('teacher_id');
            $table->dropColumn('training_coordinator_id');
        });

        Schema::dropIfExists('lesson_project_time');
    }
}
