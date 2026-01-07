<?php
namespace Database\Seeders;
use App\Models\Author;
use Illuminate\Database\Seeder;
class AdditionalAuthorsSeeder extends Seeder
{
    public function run(): void
    {
        $additionalAuthors = [
            ['name' => 'João Silva', 'bio' => 'Escritor brasileiro de romances urbanos contemporâneos.'],
            ['name' => 'Maria Santos', 'bio' => 'Poeta brasileira focada em temas sociais e femininos.'],
            ['name' => 'Pedro Oliveira', 'bio' => 'Autor de ficção científica e distopia brasileira.'],
            ['name' => 'Ana Costa', 'bio' => 'Romancista que explora a psicologia feminina moderna.'],
            ['name' => 'Carlos Ferreira', 'bio' => 'Ensaísta sobre política e sociedade brasileira.'],
            ['name' => 'Luciana Mendes', 'bio' => 'Escritora de contos fantásticos e realismo mágico.'],
            ['name' => 'Roberto Almeida', 'bio' => 'Historiador especializado em literatura brasileira.'],
            ['name' => 'Fernanda Lima', 'bio' => 'Jornalista e autora de crônicas urbanas.'],
            ['name' => 'John Anderson', 'bio' => 'Escritor especializado em ficção contemporânea.'],
            ['name' => 'Sarah Mitchell', 'bio' => 'Romancista conhecida por romances históricos.'],
            ['name' => 'Michael Thompson', 'bio' => 'Autor de romances de mistério e suspense.'],
            ['name' => 'Emma Wilson', 'bio' => 'Poeta e autora de literatura infantil.'],
            ['name' => 'David Martinez', 'bio' => 'Escritor que aborda identidade cultural e imigração.'],
            ['name' => 'Jennifer Brown', 'bio' => 'Romancista focada em dramas familiares.'],
            ['name' => 'Robert Taylor', 'bio' => 'Autor de não-ficção histórica e biografias.'],
            ['name' => 'Lisa Anderson', 'bio' => 'Autora de thrillers psicológicos e suspense.'],
            ['name' => 'James Wilson', 'bio' => 'Autor de romances de aventura e ação.'],
            ['name' => 'Patricia Moore', 'bio' => 'Poeta e crítica literária.'],
        ];
        foreach ($additionalAuthors as $author) {
            Author::updateOrCreate(
                ['name' => $author['name']],
                [
                    'bio' => $author['bio'],
                    'photo' => 'pedbook/profiles/default-user',
                ]
            );
        }
        $this->command->info('Additional authors seeded successfully!');
    }
}