<?php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:3000'], // Votre port React
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];