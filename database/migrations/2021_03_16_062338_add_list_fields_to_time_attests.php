<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddListFieldsToTimeAttests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('time_attests', function (Blueprint $table) {
            $table->unsignedInteger('from_list_by')->nullable();
            $table->unsignedInteger('project_time_id')->nullable();

            $table->foreign('from_list_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('project_time_id')
                ->references('id')
                ->on('project_times')
                ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('time_attests', function (Blueprint $table) {
            $table->dropForeign(['from_list_by']);
            $table->dropColumn('from_list_by');
            $table->dropForeign(['project_time_id']);
            $table->dropColumn('project_time_id');
        });
    }
}
