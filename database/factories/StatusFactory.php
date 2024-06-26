<?php

namespace Database\Factories;

use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class StatusFactory extends Factory
{
    protected $model = Status::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->unique()->randomElement(['Pendiente', 'Procesando','Completado','Procesamiento de retraso', 'Esperando por confirmación', 'Cerrado']);
        return [
            'name' => $name,
            'slug' => strtolower(str_replace(' ','_',$name)),
        ];
    }
}
