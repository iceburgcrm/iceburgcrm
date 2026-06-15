<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class SeedFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition($module)
    {
        $data = [];
        $faker = $this->faker();
        $module->fields()->get()->each(function ($field) use (&$data, $faker) {
            if (isset($field->list) && strlen($field->list) > 0) {
                $data[$field->name] = 1;
            } elseif ($field->name == 'first_name') {
                $data[$field->name] = $faker->firstName;
            } elseif ($field->name == 'last_name') {
                $data[$field->name] = $faker->lastName;
            } elseif ($field->input_type == 'tel') {
                $data[$field->name] = $faker->phoneNumber;
            } elseif ($field->input_type == 'tel') {
                $data[$field->name] = $faker->phoneNumber;
            } elseif ($field->input_type == 'email') {
                $data[$field->name] = $faker->email;
            } elseif ($field->input_type == 'city') {
                $data[$field->name] = $faker->city;
            } elseif ($field->input_type == 'zip') {
                $data[$field->name] = $faker->postcode;
            } elseif ($field->input_type == 'address') {
                $data[$field->name] = $faker->address;
            } elseif ($field->input_type == 'date') {
                $data[$field->name] = $faker->unixTime();
            } elseif ($field->input_type == 'currency') {
                $data[$field->name] = $faker->randomFloat(2, 1, 100);
            } elseif ($field->input_type == 'related') {
                $data[$field->name] = rand(1, 5);
            } elseif ($field->input_type == 'textarea') {
                $data[$field->name] = $faker->realTextBetween(50, 200);
            } elseif ($field->data_type == 'string') {

                $data[$field->name] = $faker->realTextBetween(10, 50);
            } elseif ($field->data_type == 'Integer') {
                $data[$field->name] = $faker->numberBetween(1, 100);
            } else {
                $data[$field->name] = 1;
            }
        });

        return $data;
    }
}
