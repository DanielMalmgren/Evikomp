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
            $table->string('personid', 12);
            $table->string('name')->nullable();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('email')->unique()->nullable();
            $table->boolean('accepted_gdpr')->default(false);
            //$table->timestamp('email_verified_at')->nullable();
            //$table->string('password')->nullable();
            $table->string('locale_id')->nullable();
            $table->foreign('locale_id')->references('id')->on('locales');
            //$table->rememberToken();
            $table->unsignedInteger('workplace_id')->nullable();
            $table->foreign('workplace_id')->references('id')->on('workplaces');
            $table->timestamps();
            $table->collation = 'utf8mb4_swedish_ci';
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
