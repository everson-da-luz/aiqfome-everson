<?php

return [
    'host' => ENV['DB_HOST'] ?? null,
    'database' => ENV['DB_DATABASE'] ?? null,
    'username' => ENV['DB_USERNAME'] ?? null,
    'password' => ENV['DB_PASSWORD'] ?? null,
    'charset' => ENV['DB_CHARSET'] ?? null
];
