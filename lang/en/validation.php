<?php

return [
    'custom' => [
        'title' => [
            'required' => 'The title is required.',
            'max' => 'The title may not be greater than 255 characters.',
        ],
        'author_id' => [
            'required' => 'The author is required.',
            'exists' => 'The selected author does not exist.',
            'required_without_all' => 'The author is required.',
        ],
        'description' => [
            'max' => 'The description may not be greater than 2000 characters.',
        ],
        'author_bio' => [
            'max' => 'The author bio may not be greater than 2000 characters.',
        ],
        'bio' => [
            'max' => 'The bio may not be greater than 2000 characters.',
        ],
        'photo' => [
            'max' => 'The photo URL may not be greater than 500 characters.',
        ],
        'author_photo' => [
            'max' => 'The author photo URL may not be greater than 500 characters.',
        ],
        'price' => [
            'numeric' => 'The price must be a number.',
            'min' => 'The price must be at least 0.',
        ],
        'author_name' => [
            'required_without_all' => 'The new author name is required when no author is selected.',
        ],
        'new_author_name' => [
            'required_without_all' => 'The new author name is required when no author is selected.',
        ],
        'id' => [
            'required' => 'The book id is required.',
            'exists' => 'The selected book does not exist.',
        ],
        'book_id' => [
            'required' => 'The book id is required.',
            'exists' => 'The selected book does not exist.',
        ],
        'return_date' => [
            'required' => 'The return date is required.',
            'date' => 'The return date must be a valid date.',
            'after' => 'The return date must be after today.',
        ],
        'name' => [
            'required' => 'The author name is required.',
            'max' => 'The author name may not be greater than 255 characters.',
            'unique' => 'An author with this name already exists.',
        ],
    ],
];
