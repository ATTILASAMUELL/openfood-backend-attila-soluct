<?php

return [
    'base_uri' => env('OPENFOODFACTS_BASE_URI', 'https://world.openfoodfacts.org/'),
    'geography' => env('OPENFOOD_GEOGRAPHY', 'world'),
    'credentials' => [
        'user_id' => env('OPENFOOD_USER_ID', 'atila-samuell'),
        'password' => env('OPENFOOD_PASSWORD', 'teste'),
    ],
    'max_results' => env('OPENFOOD_MAX_RESULTS', 1000),
    'endpoints' => [
        'session' => 'cgi/session.pl',
        'ingredients' => 'cgi/ingredients.pl',
        'suggest' => 'cgi/suggest.pl',
        'nutrients' => 'cgi/nutrients.pl',
    ],
];
