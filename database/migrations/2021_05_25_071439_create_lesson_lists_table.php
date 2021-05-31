<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('icon')->nullable();
            $table->unsignedBigInteger('color_id')->nullable();
            $table->foreign('color_id')->references('id')->on('colors')->onDelete('SET NULL');

            $table->timestamps();
        });

        //A lesson list has a polymorphic many-to-many relation to users/workplaces that has permission to use it
        Schema::create('listables', function (Blueprint $table) {
            $table->unsignedBigInteger('lesson_list_id');
            $table->foreign('lesson_list_id')
            ->references('id')
            ->on('lesson_lists')
            ->onDelete('cascade');

            $table->unsignedInteger('listable_id');
            $table->string('listable_type');
        });

        //A lesson has an ordinary many-to-many relation to the lessons in the list
        Schema::create('lesson_lesson_list', function (Blueprint $table) {
            $table->unsignedInteger('lesson_id');
            $table->unsignedBigInteger('lesson_list_id');

            $table->foreign('lesson_id')
                ->references('id')
                ->on('lessons')
                ->onDelete('cascade');

            $table->foreign('lesson_list_id')
                ->references('id')
                ->on('lesson_lists')
                ->onDelete('cascade');

            $table->primary(['lesson_id', 'lesson_list_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lesson_lists');
        Schema::dropIfExists('lesson_listables');
        Schema::dropIfExists('lesson_lesson_list');
    }
}
