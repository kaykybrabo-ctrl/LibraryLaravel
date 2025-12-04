<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            ['title' => 'Life in Silence', 'author_id' => 1, 'description' => 'Uma narrativa profunda sobre a busca pela paz interior.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-life-in-silence'],
            ['title' => 'Fragments of Everyday Life', 'author_id' => 1, 'description' => 'Pequenos momentos que compõem a grandeza da existência.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-fragments-of-everyday-life'],
            ['title' => 'Stories of the Wind', 'author_id' => 2, 'description' => 'Contos místicos que navegam entre realidade e fantasia.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-stories-of-the-wind'],
            ['title' => 'Between Noise and Calm', 'author_id' => 2, 'description' => 'Uma jornada filosófica sobre encontrar equilíbrio.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-between-noise-and-calm'],
            ['title' => 'The Horizon and the Sea', 'author_id' => 1, 'description' => 'Romance épico que explora os limites do amor.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-the-horizon-and-the-sea'],
            ['title' => 'Winds of Change', 'author_id' => 1, 'description' => 'Drama histórico sobre transformações sociais.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-winds-of-change'],
            ['title' => 'Paths of the Soul', 'author_id' => 2, 'description' => 'Reflexões espirituais sobre o propósito da vida.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-paths-of-the-soul'],
            ['title' => 'Under the Grey Sky', 'author_id' => 2, 'description' => 'Thriller psicológico ambientado em uma cidade sombria.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-under-the-grey-sky'],
            ['title' => 'Notes of a Silence', 'author_id' => 1, 'description' => 'Poesia em prosa sobre a beleza do silêncio.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-notes-of-a-silence'],
            ['title' => 'The Last Letter', 'author_id' => 2, 'description' => 'Mistério envolvente sobre segredos familiares.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-the-last-letter'],
            ['title' => 'Between Words', 'author_id' => 1, 'description' => 'Explorando o não dito e os significados ocultos.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-between-words'],
            ['title' => 'Colors of the City', 'author_id' => 2, 'description' => 'Um retrato vibrante da vida urbana.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-colors-of-the-city'],
            ['title' => 'The Weight of the Rain', 'author_id' => 1, 'description' => 'Metáfora poética sobre os fardos que carregamos.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-the-weight-of-the-rain'],
            ['title' => 'Blue Night', 'author_id' => 2, 'description' => 'Jornada misteriosa através da escuridão iluminada.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-blue-night'],
            ['title' => 'Faces of Memory', 'author_id' => 1, 'description' => 'Histórias que capturam a natureza efêmera das lembranças.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-faces-of-memory'],
            ['title' => 'Origin Tales', 'author_id' => 2, 'description' => 'Explorando as raízes e os começos que moldam quem somos.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-origin-tales'],
            ['title' => 'Fragments of Hope', 'author_id' => 1, 'description' => 'Narrativa sobre esperança e possibilidades em tempos difíceis.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-fragments-of-hope'],
            ['title' => 'Trails and Scars', 'author_id' => 2, 'description' => 'Coleção de reflexões sobre as marcas que a vida deixa.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-trails-and-scars'],
            ['title' => 'From the Other Side of the Street', 'author_id' => 1, 'description' => 'História sobre perspectivas diferentes e a beleza da diversidade.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-from-the-other-side-of-the-street'],
            ['title' => 'Interrupted Seasons', 'author_id' => 2, 'description' => 'Exploração sobre mudanças inesperadas e adaptação na vida.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-interrupted-seasons'],
        ];

        foreach ($books as $book) {
            Book::updateOrCreate(
                [
                    'title' => $book['title'],
                    'author_id' => $book['author_id'],
                ],
                [
                    'description' => $book['description'],
                    'photo' => $book['photo'],
                ]
            );
        }
    }
}
