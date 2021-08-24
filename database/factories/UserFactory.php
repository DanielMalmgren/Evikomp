<?php

namespace Database\Factories;

use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $firstname = $this->faker->firstName;
        $lastname = $this->faker->lastName;
        return [
            'name' => $firstname.' '.$lastname,
            'firstname' => $firstname,
            'saml_firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $this->faker->unique()->safeEmail,
            //'password' => '', // secret
            //'remember_token' => str_random(10),
            //'personid' => str_random(12),
            'personid' => date("Ymd", $this->faker->unixTime($max = 'now')).$this->faker->randomNumber(4, true),
            'workplace_id' => rand(1, 6),
        ];
    }
}
