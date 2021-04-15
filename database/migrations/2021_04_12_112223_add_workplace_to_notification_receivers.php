<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWorkplaceToNotificationReceivers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //TODO:
        //https://stackoverflow.com/questions/51161636/many-to-many-relationship-3-models-laravel-5-6
        //https://stackoverflow.com/questions/23137008/laravel-three-table-pivot
        Schema::dropIfExists('notification_receivers');

        Schema::create('notification_receivers', function (Blueprint $table) {
            $table->unsignedInteger('lesson_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('workplace_id');

            $table->foreign('lesson_id')
                ->references('id')
                ->on('lessons')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('workplace_id')
                ->references('id')
                ->on('workplaces')
                ->onDelete('cascade');

            $table->primary(['lesson_id', 'user_id', 'workplace_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_receivers');

        Schema::create('notification_receivers', function (Blueprint $table) {
            $table->unsignedInteger('lesson_id');
            $table->unsignedInteger('user_id');

            $table->foreign('lesson_id')
                ->references('id')
                ->on('lessons')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->primary(['lesson_id', 'user_id']);
        });
    }
}
