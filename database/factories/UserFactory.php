<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    $firstname = $faker->firstName;
    $lastname = $faker->lastName;
    return [
        'name' => $firstname.' '.$lastname,
        'firstname' => $firstname,
        'lastname' => $lastname,
        'email' => $faker->unique()->safeEmail,
        //'password' => '', // secret
        //'remember_token' => str_random(10),
        //'personid' => str_random(12),
        'personid' => date("Ymd", $faker->unixTime($max = 'now')).$faker->randomNumber(4, true),
        'workplace_id' => rand(1, 6),
    ];
});
