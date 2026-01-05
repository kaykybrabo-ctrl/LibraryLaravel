<?php
namespace Database\Seeders;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Database\Seeder;
class BookSeeder extends Seeder
{
    public function run(): void
    {
        $authors = [
            ['name' => 'Guilherme Biondo', 'bio' => 'Escritor renomado com obras que exploram a profundidade da alma humana.'],
            ['name' => 'Manoel Leite', 'bio' => 'Autor contemporâneo que mescla filosofia e narrativa em suas obras.'],
            ['name' => 'Ana Clara Silva', 'bio' => 'Romancista que explora temas femininos e empoderamento.'],
            ['name' => 'Carlos Mendes', 'bio' => 'Poeta e ensaísta focado em literatura brasileira contemporânea.'],
            ['name' => 'Mariana Costa', 'bio' => 'Escritora de ficção científica e fantasia urbana.'],
            ['name' => 'Roberto Alves', 'bio' => 'Historiador e autor de não-ficção sobre cultura brasileira.'],
            ['name' => 'Laura Ferreira', 'bio' => 'Jornalista e escritora de crônicas urbanas.'],
            ['name' => 'Pedro Santos', 'bio' => 'Autor de thrillers e romances policiais.'],
            ['name' => 'Camila Oliveira', 'bio' => 'Poeta e contista com obras sobre natureza e ecologia.'],
            ['name' => 'Lucas Pereira', 'bio' => 'Filósofo e escritor de ensaios sobre ética e sociedade.'],
            ['name' => 'Isabella Rocha', 'bio' => 'Romancista de histórias de amor e superação.'],
            ['name' => 'Bruno Carvalho', 'bio' => 'Autor de ficção juvenil e literatura infantojuvenil.'],
            ['name' => 'Fernanda Lima', 'bio' => 'Escritora de memórias e biografias inspiradoras.'],
            ['name' => 'Gustavo Henrique', 'bio' => 'Cronista de costumes e tradiciones populares.'],
            ['name' => 'Tatiana Ribeiro', 'bio' => 'Autora de contos fantásticos e literatura fantástica.'],
        ];
        $authorModels = [];
        foreach ($authors as $author) {
            $authorModel = Author::firstOrCreate(['name' => $author['name']], [
                'bio' => $author['bio'],
                'photo' => 'pedbook/profiles/default-user',
            ]);
            $authorModels[] = $authorModel;
        }
        $books = [
            ['title' => 'Life in Silence', 'author_id' => $authorModels[0]->id, 'description' => 'Uma narrativa profunda sobre a busca pela paz interior.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-life-in-silence', 'price' => 39.90],
            ['title' => 'Fragments of Everyday Life', 'author_id' => $authorModels[0]->id, 'description' => 'Pequenos momentos que compõem a grandeza da existência.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-fragments-of-everyday-life', 'price' => 34.90],
            ['title' => 'Stories of the Wind', 'author_id' => $authorModels[1]->id, 'description' => 'Contos místicos que navegam entre realidade e fantasia.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-stories-of-the-wind', 'price' => 29.90],
            ['title' => 'Between Noise and Calm', 'author_id' => $authorModels[1]->id, 'description' => 'Uma jornada filosófica sobre encontrar equilíbrio.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-between-noise-and-calm', 'price' => 44.90],
            ['title' => 'The Horizon and the Sea', 'author_id' => $authorModels[0]->id, 'description' => 'Romance épico que explora os limites do amor.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-the-horizon-and-the-sea', 'price' => 49.90],
            ['title' => 'Winds of Change', 'author_id' => $authorModels[0]->id, 'description' => 'Drama histórico sobre transformações sociais.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-winds-of-change', 'price' => 36.90],
            ['title' => 'Paths of the Soul', 'author_id' => $authorModels[1]->id, 'description' => 'Reflexões espirituais sobre o propósito da vida.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-paths-of-the-soul', 'price' => 27.90],
            ['title' => 'Under the Grey Sky', 'author_id' => $authorModels[1]->id, 'description' => 'Thriller psicológico ambientado em uma cidade sombria.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-under-the-grey-sky', 'price' => 41.90],
            ['title' => 'Notes of a Silence', 'author_id' => $authorModels[0]->id, 'description' => 'Poesia em prosa sobre a beleza do silêncio.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-notes-of-a-silence', 'price' => 22.90],
            ['title' => 'The Last Letter', 'author_id' => $authorModels[1]->id, 'description' => 'Mistério envolvente sobre segredos familiares.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-the-last-letter', 'price' => 33.90],
            ['title' => 'Between Words', 'author_id' => $authorModels[0]->id, 'description' => 'Explorando o não dito e os significados ocultos.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-between-words', 'price' => 24.90],
            ['title' => 'Colors of the City', 'author_id' => $authorModels[1]->id, 'description' => 'Um retrato vibrante da vida urbana.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-colors-of-the-city', 'price' => 31.90],
            ['title' => 'The Weight of the Rain', 'author_id' => $authorModels[0]->id, 'description' => 'Metáfora poética sobre os fardos que carregamos.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-the-weight-of-the-rain', 'price' => 26.90],
            ['title' => 'Blue Night', 'author_id' => $authorModels[1]->id, 'description' => 'Jornada misteriosa através da escuridão iluminada.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-blue-night', 'price' => 28.90],
            ['title' => 'Faces of Memory', 'author_id' => $authorModels[0]->id, 'description' => 'Histórias que capturam a natureza efêmera das lembranças.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-faces-of-memory', 'price' => 37.90],
            ['title' => 'Origin Tales', 'author_id' => $authorModels[1]->id, 'description' => 'Explorando as raízes e os começos que moldam quem somos.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-origin-tales', 'price' => 32.90],
            ['title' => 'Fragments of Hope', 'author_id' => $authorModels[0]->id, 'description' => 'Narrativa sobre esperança e possibilidades em tempos difíceis.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-fragments-of-hope', 'price' => 35.90],
            ['title' => 'Trails and Scars', 'author_id' => $authorModels[1]->id, 'description' => 'Coleção de reflexões sobre as marcas que a vida deixa.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-trails-and-scars', 'price' => 29.90],
            ['title' => 'From the Other Side of the Street', 'author_id' => $authorModels[0]->id, 'description' => 'História sobre perspectivas diferentes e a beleza da diversidade.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-from-the-other-side-of-the-street', 'price' => 45.90],
            ['title' => 'Interrupted Seasons', 'author_id' => $authorModels[1]->id, 'description' => 'Exploração sobre mudanças inesperadas e adaptação na vida.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-interrupted-seasons', 'price' => 38.90],
            ['title' => 'Women Who Run with Wolves', 'author_id' => $authorModels[2]->id, 'description' => 'Mitologia feminina e arquétipos da mulher moderna.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-women-who-run', 'price' => 52.90],
            ['title' => 'The Alchemist', 'author_id' => $authorModels[3]->id, 'description' => 'Jornada de autoconhecimento e busca por significado.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-the-alchemist', 'price' => 42.90],
            ['title' => 'Neuromancer', 'author_id' => $authorModels[4]->id, 'description' => 'Clássico da ficção científica cyberpunk.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-neuromancer', 'price' => 47.90],
            ['title' => 'The Name of the Rose', 'author_id' => $authorModels[5]->id, 'description' => 'Mistério medieval na abadia beneditina.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-name-of-the-rose', 'price' => 54.90],
            ['title' => 'City of Glass', 'author_id' => $authorModels[6]->id, 'description' => 'Crônicas poéticas da metrópole contemporânea.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-city-of-glass', 'price' => 31.90],
            ['title' => 'The Girl with the Dragon Tattoo', 'author_id' => $authorModels[7]->id, 'description' => 'Thriller nórdico sobre mistérios familiares.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-dragon-tattoo', 'price' => 44.90],
            ['title' => 'The Secret Life of Trees', 'author_id' => $authorModels[8]->id, 'description' => 'Exploração da comunicação e consciência das árvores.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-secret-life-trees', 'price' => 38.90],
            ['title' => 'Being and Time', 'author_id' => $authorModels[9]->id, 'description' => 'Análise existencial da condição humana.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-being-and-time', 'price' => 58.90],
            ['title' => 'Pride and Prejudice', 'author_id' => $authorModels[10]->id, 'description' => 'Romance clássico sobre amor e preconceito social.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-pride-prejudice', 'price' => 35.90],
            ['title' => 'The Little Prince', 'author_id' => $authorModels[11]->id, 'description' => 'Fábula filosófica sobre amizade e perda.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-little-prince', 'price' => 28.90],
            ['title' => 'Long Walk to Freedom', 'author_id' => $authorModels[12]->id, 'description' => 'Autobiografia de Nelson Mandela e luta contra apartheid.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-long-walk-freedom', 'price' => 62.90],
            ['title' => 'Cultural Anthropology', 'author_id' => $authorModels[13]->id, 'description' => 'Estudo sobre costumes e tradições brasileiras.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-cultural-anthropology', 'price' => 48.90],
            ['title' => 'The Lord of the Rings', 'author_id' => $authorModels[14]->id, 'description' => 'Épica fantástica sobre amizade e coragem.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-lord-of-the-rings', 'price' => 69.90],
            ['title' => 'One Hundred Years of Solitude', 'author_id' => $authorModels[0]->id, 'description' => 'Crônica mágica da família Buendía.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-one-hundred-years', 'price' => 55.90],
            ['title' => 'The Shadow of the Wind', 'author_id' => $authorModels[1]->id, 'description' => 'Mistério em Barcelona durante a guerra civil.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-shadow-of-the-wind', 'price' => 46.90],
            ['title' => 'The Kite Runner', 'author_id' => $authorModels[2]->id, 'description' => 'História de amizade e redenção no Afeganistão.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-kite-runner', 'price' => 41.90],
            ['title' => 'The Book Thief', 'author_id' => $authorModels[3]->id, 'description' => 'História comovente sobre uma garota na Alemanha nazista.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-thief', 'price' => 37.90],
            ['title' => 'Dune', 'author_id' => $authorModels[4]->id, 'description' => 'Epíco de ficção científica sobre política e religião.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-dune', 'price' => 53.90],
            ['title' => 'The Da Vinci Code', 'author_id' => $authorModels[5]->id, 'description' => 'Thriller sobre conspirações na Igreja Católica.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-da-vinci-code', 'price' => 43.90],
            ['title' => 'The Catcher in the Rye', 'author_id' => $authorModels[6]->id, 'description' => 'Clássico sobre adolescência e alienação.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-catcher-in-the-rye', 'price' => 33.90],
            ['title' => 'Gone Girl', 'author_id' => $authorModels[7]->id, 'description' => 'Thriller psicológico sobre desaparecimento misterioso.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-gone-girl', 'price' => 39.90],
            ['title' => 'The Silent Spring', 'author_id' => $authorModels[8]->id, 'description' => 'Denúncia sobre os perigos dos pesticidas.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-silent-spring', 'price' => 35.90],
            ['title' => 'The Republic', 'author_id' => $authorModels[9]->id, 'description' => 'Diálogo sobre justiça e a sociedade ideal.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-republic', 'price' => 49.90],
            ['title' => 'Jane Eyre', 'author_id' => $authorModels[10]->id, 'description' => 'Romance gótico sobre independência feminina.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-jane-eyre', 'price' => 36.90],
            ['title' => 'The Chronicles of Narnia', 'author_id' => $authorModels[11]->id, 'description' => 'Fantasia cristã sobre aventuras em um mundo mágico.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-narnia', 'price' => 42.90],
            ['title' => 'Steve Jobs', 'author_id' => $authorModels[12]->id, 'description' => 'Biografia autorizada do fundador da Apple.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-steve-jobs', 'price' => 47.90],
            ['title' => 'Folklore and Popular Culture', 'author_id' => $authorModels[13]->id, 'description' => 'Estudo sobre tradições populares brasileiras.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-folklore-culture', 'price' => 38.90],
            ['title' => 'Harry Potter and the Sorcerer\'s Stone', 'author_id' => $authorModels[14]->id, 'description' => 'Início da saga sobre o bruxo e seus amigos.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-harry-potter', 'price' => 45.90],
            ['title' => 'The Hobbit', 'author_id' => $authorModels[0]->id, 'description' => 'Aventura de Bilbo Bolseiro na Terra Média.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-hobbit', 'price' => 34.90],
            ['title' => 'The Pillars of the Earth', 'author_id' => $authorModels[1]->id, 'description' => 'Saga medieval sobre construção de catedral gótica.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-pillars-of-earth', 'price' => 56.90],
            ['title' => 'The Time Traveler\'s Wife', 'author_id' => $authorModels[2]->id, 'description' => 'Romance sobre amor através do tempo e espaço.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-time-traveler-wife', 'price' => 40.90],
            ['title' => 'The Road', 'author_id' => $authorModels[3]->id, 'description' => 'Distopia pós-apocalíptica sobre pai e filho.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-the-road', 'price' => 32.90],
            ['title' => 'The Martian', 'author_id' => $authorModels[4]->id, 'description' => 'Sobrevivência em Marte usando ciência e engenharia.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-martian', 'price' => 44.90],
            ['title' => 'Angels & Demons', 'author_id' => $authorModels[5]->id, 'description' => 'Thriller sobre conflito entre Vaticano e illuminati.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-angels-demons', 'price' => 41.90],
            ['title' => 'On Writing', 'author_id' => $authorModels[6]->id, 'description' => 'Memórias e conselhos sobre a arte de escrever.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-on-writing', 'price' => 37.90],
            ['title' => 'The Shining', 'author_id' => $authorModels[7]->id, 'description' => 'Horror sobre hotel isolado e possessão.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-shining', 'price' => 38.90],
            ['title' => 'A Brief History of Time', 'author_id' => $authorModels[8]->id, 'description' => 'Exploração do universo do Big Bang aos buracos negros.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-brief-history-time', 'price' => 43.90],
            ['title' => 'Ethics', 'author_id' => $authorModels[9]->id, 'description' => 'Fundamentos da filosofia moral aristotélica.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-ethics', 'price' => 46.90],
            ['title' => 'Wuthering Heights', 'author_id' => $authorModels[10]->id, 'description' => 'Romance gótico sobre vingança e paixão destrutiva.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-wuthering-heights', 'price' => 35.90],
            ['title' => 'Alice in Wonderland', 'author_id' => $authorModels[11]->id, 'description' => 'Clássico da literatura infantil sobre sonhos e lógica.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-alice-wonderland', 'price' => 29.90],
            ['title' => 'Educated', 'author_id' => $authorModels[12]->id, 'description' => 'Memórias sobre educação e busca por conhecimento.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-educated', 'price' => 39.90],
            ['title' => 'Brazil: A Biography', 'author_id' => $authorModels[13]->id, 'description' => 'História completa do Brasil descobrimento aos dias atuais.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-brazil-biography', 'price' => 52.90],
            ['title' => 'The Magicians', 'author_id' => $authorModels[14]->id, 'description' => 'Fantasia moderna sobre escola de magia.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-magicians', 'price' => 41.90],
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
                    'price' => $book['price'],
                ]
            );
        }
        $minBooks = 50;
        $current = Book::count();
        if ($current < $minBooks) {
            $authorIds = collect($authorModels)->pluck('id')->all();
            $missing = $minBooks - $current;
            for ($i = 0; $i < $missing; $i++) {
                $authorId = $authorIds[array_rand($authorIds)];
                Book::factory()->create([
                    'author_id' => $authorId,
                    'price' => random_int(15, 90),
                ]);
            }
        }
    }
}
