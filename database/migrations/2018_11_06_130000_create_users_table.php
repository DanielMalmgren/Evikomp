<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('personid', 12)->unique();
            $table->string('name')->nullable();
            $table->string('firstname')->nullable();
            $table->string('saml_firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->string('mobile')->nullable();
            $table->unsignedTinyInteger('terms_of_employment')->nullable();
            $table->unsignedTinyInteger('full_or_part_time')->nullable();
            $table->boolean('accepted_gdpr')->default(false);
            $table->string('locale_id')->default('sv_SE');
            $table->foreign('locale_id')->references('id')->on('locales');
            $table->unsignedInteger('workplace_id')->nullable();
            $table->foreign('workplace_id')->references('id')->on('workplaces');
            $table->unsignedInteger('title_id')->nullable();
            $table->foreign('title_id')->references('id')->on('titles');
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
        Schema::dropIfExists('users');
    }
}
