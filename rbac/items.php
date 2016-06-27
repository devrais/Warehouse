<?php
return [
    'updateProduct' => [
        'type' => 2,
        'description' => 'Update a product',
    ],
    'createProduct' => [
        'type' => 2,
        'description' => 'Create a product',
    ],
    'deleteProduct' => [
        'type' => 2,
        'description' => 'Delete a product',
    ],
    'updateCategory' => [
        'type' => 2,
        'description' => 'Update a category',
    ],
    'createCategory' => [
        'type' => 2,
        'description' => 'Create a category',
    ],
    'deleteCategory' => [
        'type' => 2,
        'description' => 'Delete a category',
    ],
    'updateEmployee' => [
        'type' => 2,
        'description' => 'Update employee info',
    ],
    'createEmployee' => [
        'type' => 2,
        'description' => 'Hire new employee',
    ],
    'deleteEmployee' => [
        'type' => 2,
        'description' => 'Delete employee',
    ],
    'owner' => [
        'type' => 1,
        'children' => [
            'createProduct',
            'updateProduct',
            'deleteProduct',
            'createCategory',
            'updateCategory',
            'deleteCategory',
            'createEmployee',
            'updateEmployee',
            'deleteEmployee',
        ],
    ],
    'manager' => [
        'type' => 1,
        'children' => [
            'createProduct',
            'updateProduct',
            'deleteProduct',
            'createCategory',
            'updateCategory',
        ],
    ],
    'worker' => [
        'type' => 1,
        'children' => [
            'createProduct',
            'updateProduct',
            'deleteProduct',
        ],
    ],
];
