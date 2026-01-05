<?php
namespace Database\Seeders;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Database\Seeder;
class AdditionalBooksSeeder extends Seeder
{
    public function run(): void
    {
        $authors = Author::all();
        $additionalBooks = [
            ['title' => 'Caminho das Estrelas', 'author_id' => $authors[2]->id, 'description' => 'Jornada cósmica sobre autoconhecimento e destino.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-caminho-estrelas', 'price' => 48.90],
            ['title' => 'Echoes of Time', 'author_id' => $authors[3]->id, 'description' => 'Reflexões filosóficas sobre a natureza do tempo e da existência.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-echoes-time', 'price' => 44.90],
            ['title' => 'Mundos Paralelos', 'author_id' => $authors[4]->id, 'description' => 'Ficção científica sobre realidades alternativas e universos paralelos.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-mundos-paralelos', 'price' => 51.90],
            ['title' => 'The Silent Forest', 'author_id' => $authors[5]->id, 'description' => 'Mistério ambiental na floresta amazônica e segredos da natureza.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-silent-forest', 'price' => 38.90],
            ['title' => 'Rios da Memória', 'author_id' => $authors[6]->id, 'description' => 'Crônicas poéticas sobre a identidade brasileira e memória cultural.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-rios-memoria', 'price' => 33.90],
            ['title' => 'Shadows of Justice', 'author_id' => $authors[7]->id, 'description' => 'Thriller policial sobre corrupção, poder e redenção.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-shadows-justice', 'price' => 42.90],
            ['title' => 'Verde Esperança', 'author_id' => $authors[8]->id, 'description' => 'Poesia ecológica sobre sustentabilidade e futuro do planeta.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-verde-esperanca', 'price' => 29.90],
            ['title' => 'The Human Condition', 'author_id' => $authors[9]->id, 'description' => 'Análise filosófica da existência humana na sociedade moderna.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-human-condition', 'price' => 56.90],
            ['title' => 'Corações em Tempestade', 'author_id' => $authors[10]->id, 'description' => 'Romance sobre amor que supera todas as adversidades da vida.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-coracoes-tempestade', 'price' => 37.90],
            ['title' => 'The Lost Kingdom', 'author_id' => $authors[11]->id, 'description' => 'Aventura fantástica para jovens leitores sobre reinos perdidos.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-lost-kingdom', 'price' => 31.90],
            ['title' => 'Mulheres de Fogo', 'author_id' => $authors[12]->id, 'description' => 'Biografias inspiradoras de mulheres que mudaram o mundo.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-mulheres-fogo', 'price' => 45.90],
            ['title' => 'Voices of Brazil', 'author_id' => $authors[13]->id, 'description' => 'Estudo antropológico das diversas culturas brasileiras.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-voices-brazil', 'price' => 40.90],
            ['title' => 'Sonhos de Papel', 'author_id' => $authors[14]->id, 'description' => 'Contos fantásticos sobre o poder da imaginação e criatividade.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-sonhos-papel', 'price' => 34.90],
            ['title' => 'The Digital Age', 'author_id' => $authors[15]->id, 'description' => 'Análise sociológica dos impactos da era digital na sociedade.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-digital-age', 'price' => 47.90],
            ['title' => 'Pontes do Amanhã', 'author_id' => $authors[16]->id, 'description' => 'Ficção distópica sobre esperança e reconstrução do futuro.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-pontes-amanha', 'price' => 35.90],
            ['title' => 'The Last Garden', 'author_id' => $authors[17]->id, 'description' => 'Distopia ecológica sobre sobrevivência em mundo pós-catástrofe.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-last-garden', 'price' => 43.90],
            ['title' => 'Labirintos da Mente', 'author_id' => $authors[18]->id, 'description' => 'Ficção científica sobre consciência, realidade e percepção.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-labirintos-mente', 'price' => 49.90],
            ['title' => 'The Forgotten City', 'author_id' => $authors[19]->id, 'description' => 'Mistério histórico sobre civilizações perdidas e descobertas.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-forgotten-city', 'price' => 41.90],
            ['title' => 'Vozes do Silêncio', 'author_id' => $authors[0]->id, 'description' => 'Poesia profunda sobre comunicação não-verbal e emoções.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-vozes-silencio', 'price' => 32.90],
            ['title' => 'The Midnight Sun', 'author_id' => $authors[1]->id, 'description' => 'Romance nórdico sobre o sol da meia-noite e amor impossível.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-midnight-sun', 'price' => 46.90],
            ['title' => 'Caminhos de Luz', 'author_id' => $authors[2]->id, 'description' => 'Jornada espiritual sobre iluminação interior e paz.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-caminhos-luz', 'price' => 38.90],
            ['title' => 'Ocean Whispers', 'author_id' => $authors[3]->id, 'description' => 'Mistério marítimo sobre segredos ocultos do oceano profundo.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-ocean-whispers', 'price' => 44.90],
            ['title' => 'Portais do Tempo', 'author_id' => $authors[4]->id, 'description' => 'Ficção científica sobre viagem no tempo e paradoxos temporais.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-portais-tempo', 'price' => 52.90],
            ['title' => 'The Hidden Path', 'author_id' => $authors[5]->id, 'description' => 'Aventura sobre trilhas secretas e mistérios da floresta.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-hidden-path', 'price' => 36.90],
            ['title' => 'Estrelas Cadentes', 'author_id' => $authors[6]->id, 'description' => 'Romance sobre sonhos, desejos e realizações pessoais.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-estrelas-cadentes', 'price' => 41.90],
            ['title' => 'Crystal Dreams', 'author_id' => $authors[7]->id, 'description' => 'Fantasia sobre cristais mágicos e poderes sobrenaturais.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-crystal-dreams', 'price' => 47.90],
            ['title' => 'Montanhas do Destino', 'author_id' => $authors[8]->id, 'description' => 'Aventura épica nas montanhas sagradas e provações.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-montanhas-destino', 'price' => 54.90],
            ['title' => 'The Silver Moon', 'author_id' => $authors[9]->id, 'description' => 'Romance gótico sob a lua prateada e mistérios noturnos.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-silver-moon', 'price' => 39.90],
            ['title' => 'Rios do Tempo', 'author_id' => $authors[10]->id, 'description' => 'Filosofia sobre a natureza cíclica do tempo e destino.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-rios-tempo', 'price' => 45.90],
            ['title' => 'Golden Horizons', 'author_id' => $authors[11]->id, 'description' => 'Romance sobre novos começos, esperança e superação.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-golden-horizons', 'price' => 42.90],
            ['title' => 'Nuvens de Pensamento', 'author_id' => $authors[12]->id, 'description' => 'Poesia sobre a natureza da consciência e pensamento humano.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-nuvens-pensamento', 'price' => 33.90],
            ['title' => 'The Emerald Forest', 'author_id' => $authors[13]->id, 'description' => 'Aventura na floresta esmeralda e segredos da natureza.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-emerald-forest', 'price' => 48.90],
            ['title' => 'Espelhos da Alma', 'author_id' => $authors[14]->id, 'description' => 'Reflexões profundas sobre autoconhecimento e identidade.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-espelhos-alma', 'price' => 37.90],
            ['title' => 'Shadow Dancers', 'author_id' => $authors[15]->id, 'description' => 'Thriller sobre dançarinos e segredos ocultos do mundo artístico.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-shadow-dancers', 'price' => 44.90],
            ['title' => 'Fogo e Gelo', 'author_id' => $authors[16]->id, 'description' => 'Romance sobre opostos que se atraem e paixões intensas.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-fogo-gelo', 'price' => 35.90],
            ['title' => 'The Crystal Cave', 'author_id' => $authors[17]->id, 'description' => 'Fantasia sobre cavernas de cristal mágico e aventuras.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-crystal-cave', 'price' => 51.90],
            ['title' => 'Ventos da Mudança', 'author_id' => $authors[18]->id, 'description' => 'Drama sobre transformações pessoais e renovação interior.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-ventos-mudanca', 'price' => 38.90],
            ['title' => 'The Northern Lights', 'author_id' => $authors[19]->id, 'description' => 'Romance sob as luzes do norte e amor ártico.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-northern-lights', 'price' => 46.90],
            ['title' => 'Caminhos da Vida', 'author_id' => $authors[20]->id, 'description' => 'Reflexões profundas sobre as escolhas e caminhos da vida.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-caminhos-vida', 'price' => 32.90],
            ['title' => 'Ocean Depths', 'author_id' => $authors[21]->id, 'description' => 'Mistério nas profundezas do oceano e criaturas marinhas.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-ocean-depths', 'price' => 49.90],
            ['title' => 'Sonhos e Realidade', 'author_id' => $authors[22]->id, 'description' => 'Ficção sobre a fronteira entre sonho e realidade.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-sonhos-realidade', 'price' => 41.90],
            ['title' => 'The Digital Soul', 'author_id' => $authors[23]->id, 'description' => 'Ficção científica sobre consciência digital e identidade virtual.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-digital-soul', 'price' => 54.90],
            ['title' => 'Portais do Infinito', 'author_id' => $authors[24]->id, 'description' => 'Fantasia sobre portais para outros mundos e dimensões.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-portais-infinito', 'price' => 47.90],
            ['title' => 'The Last Dawn', 'author_id' => $authors[25]->id, 'description' => 'Distopia sobre o último amanhecimento da humanidade.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-last-dawn', 'price' => 43.90],
            ['title' => 'Labirintos do Coração', 'author_id' => $authors[26]->id, 'description' => 'Romance sobre os labirintos do amor e relacionamentos.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-labirintos-coracao', 'price' => 39.90],
            ['title' => 'The Forgotten Kingdom', 'author_id' => $authors[27]->id, 'description' => 'Aventura sobre reinos perdidos e tesouros antigos.', 'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/books/book-forgotten-kingdom', 'price' => 52.90],
        ];
        foreach ($additionalBooks as $book) {
            Book::updateOrCreate(
                ['title' => $book['title'], 'author_id' => $book['author_id']],
                [
                    'description' => $book['description'],
                    'photo' => $book['photo'],
                    'price' => $book['price'],
                ]
            );
        }
        $this->command->info('Additional books seeded successfully!');
    }
}
