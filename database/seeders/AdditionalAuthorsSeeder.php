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
            ['name' => 'John Anderson', 'bio' => 'American writer specializing in contemporary fiction.'],
            ['name' => 'Sarah Mitchell', 'bio' => 'British novelist known for historical romance.'],
            ['name' => 'Michael Thompson', 'bio' => 'Canadian author of mystery novels.'],
            ['name' => 'Emma Wilson', 'bio' => 'Australian poet and children\'s literature author.'],
            ['name' => 'David Martinez', 'bio' => 'Spanish-American writer on cultural identity.'],
            ['name' => 'Jennifer Brown', 'bio' => 'American novelist focusing on family dramas.'],
            ['name' => 'Robert Taylor', 'bio' => 'British author of historical non-fiction.'],
            ['name' => 'Lisa Anderson', 'bio' => 'Canadian writer of psychological thrillers.'],
            ['name' => 'James Wilson', 'bio' => 'Australian author of adventure novels.'],
            ['name' => 'Patricia Moore', 'bio' => 'American poet and literary critic.'],
        ];
        foreach ($additionalAuthors as $author) {
            Author::firstOrCreate(['name' => $author['name']], [
                'bio' => $author['bio'],
                'photo' => 'pedbook/profiles/default-user',
            ]);
        }
        $this->command->info('Additional authors seeded successfully!');
    }
}