<?php

return [
    // pdo driver
    'driver' => 'sqlite', // recommended: mysql, sqlite

    // SQLite db filepath (if driver is sqlite)
    'file' => root_dir('/storage/main.db'),

    // database charset
    'charset' => 'utf8mb4',
    'collate' => 'utf8mb4_unicode_ci',

    // database configuration
    'name' => '',
    'host' => '',
    'port' => '',
    'user' => '',
    'password' => '',
];
