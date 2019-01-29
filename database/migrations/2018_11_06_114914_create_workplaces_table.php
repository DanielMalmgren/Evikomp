<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkplacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workplaces', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('municipality_id');
            $table->foreign('municipality_id')->references('id')->on('municipalities');
            $table->unsignedInteger('workplace_type_id');
            $table->foreign('workplace_type_id')->references('id')->on('workplace_types');
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
        Schema::dropIfExists('workplaces');
    }
}
