<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimeAttestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_attests', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->unsignedDecimal('hours', 4, 1);
            $table->unsignedTinyInteger('attestlevel');
            $table->unsignedInteger('user_id'); //User whose time is getting attested
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedInteger('attestant_id'); //User doing the attest (will be same as user_id for level 1 attests)
            $table->foreign('attestant_id')->references('id')->on('users');
            $table->string('clientip', 39); //39 is the longest possible IPv6 address length
            $table->string('authnissuer'); //Issuer of authentication used for login
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
        Schema::dropIfExists('time_attests');
    }
}
