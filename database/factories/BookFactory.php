<?php
namespace Database\Factories;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;
class BookFactory extends Factory
{
    protected $model = Book::class;
    public function definition(): array
    {
        static $faker = null;
        if (!$faker) {
            $faker = \Faker\Factory::create('pt_BR');
        }
        return [
            'title' => $faker->sentence(3),
            'description' => $faker->paragraph(3),
            'photo' => 'pedbook/books/default-book',
            'author_id' => Author::factory(),
        ];
    }
}
