<?php
namespace Database\Factories;
use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;
class AuthorFactory extends Factory
{
    protected $model = Author::class;
    public function definition(): array
    {
        static $faker = null;
        if (!$faker) {
            $faker = \Faker\Factory::create('pt_BR');
        }
        return [
            'name' => $faker->name(),
            'bio' => $faker->paragraph(),
            'photo' => 'pedbook/profiles/default-user',
        ];
    }
}
