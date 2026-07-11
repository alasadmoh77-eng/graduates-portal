<?php

return [
    'default' => env('STUDENT_AFFAIRS_DRIVER', 'database'),

    'drivers' => [
        'database' => [],
        'excel' => [],
        'api' => [
            'url' => env('STUDENT_AFFAIRS_API_URL', 'http://api.university.edu/v1'),
            'token' => env('STUDENT_AFFAIRS_API_TOKEN'),
        ],
    ],
];
