<?php
namespace Database\Seeders;
use App\Models\Author;
use Illuminate\Database\Seeder;
class AuthorSeeder extends Seeder
{
    public function run(): void
    {
        Author::updateOrCreate(
            ['name' => 'Guilherme Biondo'],
            [
                'bio' => 'Escritor renomado com obras que exploram a profundidade da alma humana.',
                'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/profiles/author-guilherme-biondo.jpg',
            ]
        );
        Author::updateOrCreate(
            ['name' => 'Manoel Leite'],
            [
                'bio' => 'Autor contemporâneo que mescla filosofia e narrativa em suas obras.',
                'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/profiles/author-manoel-leite.jpg',
            ]
        );
        $minAuthors = 15;
        $current = Author::count();
        if ($current < $minAuthors) {
            Author::factory()->count($minAuthors - $current)->create();
        }
    }
}
