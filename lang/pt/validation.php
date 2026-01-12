<?php

return [
    'min' => [
        'string' => 'O campo :attribute deve ter pelo menos :min caracteres.',
    ],
    'custom' => [
        'title' => [
            'required' => 'O título é obrigatório.',
            'max' => 'O título não pode ter mais de 255 caracteres.',
        ],
        'author_id' => [
            'required' => 'O autor é obrigatório.',
            'exists' => 'O autor selecionado não existe.',
            'required_without_all' => 'O autor é obrigatório.',
        ],
        'description' => [
            'max' => 'A descrição não pode ter mais de 2000 caracteres.',
        ],
        'author_bio' => [
            'max' => 'A descrição não pode ter mais de 2000 caracteres.',
        ],
        'bio' => [
            'max' => 'A biografia não pode ter mais de 2000 caracteres.',
        ],
        'photo' => [
            'max' => 'A URL da foto não pode ter mais de 500 caracteres.',
        ],
        'author_photo' => [
            'max' => 'A URL da foto não pode ter mais de 500 caracteres.',
        ],
        'price' => [
            'numeric' => 'O preço deve ser um número.',
            'min' => 'O preço não pode ser negativo.',
        ],
        'author_name' => [
            'required_without_all' => 'O nome do novo autor é obrigatório quando nenhum autor é selecionado.',
        ],
        'new_author_name' => [
            'required_without_all' => 'O nome do novo autor é obrigatório quando nenhum autor é selecionado.',
        ],
        'id' => [
            'required' => 'O ID do livro é obrigatório.',
            'exists' => 'O livro selecionado não existe.',
        ],
        'book_id' => [
            'required' => 'O ID do livro é obrigatório.',
            'exists' => 'O livro selecionado não existe.',
        ],
        'return_date' => [
            'required' => 'A data de devolução é obrigatória.',
            'date' => 'A data de devolução deve ser uma data válida.',
            'after' => 'A data de devolução deve ser posterior a hoje.',
        ],
        'name' => [
            'required' => 'O nome do autor é obrigatório.',
            'max' => 'O nome do autor não pode ter mais de 255 caracteres.',
            'unique' => 'Já existe um autor com este nome.',
        ],
        'password' => [
            'min' => 'A senha deve ter pelo menos :min caracteres.',
        ],
    ],
    'attributes' => [
        'password' => 'senha',
    ],
];
