<?php

namespace VulcanPhp\Core\Console;

return [
    [
        'command' => ['serve', '-s'],
        'info'  => 'Start PHP Developement CLI and serve the application',
        'callback' => [Callback::class, 'serve']
    ],
    [
        'command' => ['help', '-h'],
        'info'  => 'Get help with vulcan CLI command',
        'callback' => [Callback::class, 'help']
    ],
    [
        'command' => ['tailwind', 'tw'],
        'action' => ['watch', '-w'],
        'info'  => 'Run TailwindCss Compiler to Watch Css Changes',
        'callback' => [Callback::class, 'tailwindWatch']
    ],
    [
        'command' => ['tailwind', 'tw'],
        'action' => ['minify', '-m'],
        'info'  => 'Run TailwindCss Compiler to Minify dist/bundle.min.css',
        'callback' => [Callback::class, 'tailwindMinify']
    ],
    [
        'command' => 'vite',
        'info'  => 'Initialize Vite Startup Application',
        'callback' => [Vite::class, 'generate']
    ],
    [
        'command' => ['make', 'mk'],
        'action' => ['table', 'migration', 'schema', '-t'],
        'alias'  => '-t',
        'info'  => 'Create a New Database Table Schema Builder With Blueprint',
        'callback' => [Callback::class, 'table']
    ],
    [
        'command' => ['make', 'mk'],
        'action' => ['seeder', 'seed', '-s'],
        'alias'  => '-s',
        'info'  => 'Create a new seeder file for database inserting dummy data',
        'callback' => [Callback::class, 'seeder']
    ],
    [
        'command' => ['make', 'mk'],
        'action' => ['controller', 'control', '-c'],
        'alias' => '-c',
        'info'  => 'Create a Controller Class for public or admin functionality',
        'callback' => [Callback::class, 'controller']
    ],
    [
        'command' => ['make', 'mk'],
        'action' => ['model', '-m'],
        'alias' => '-m',
        'info'  => 'Database Modeling functionality with Query Builder and ORM',
        'callback' => [Callback::class, 'model']
    ],
    [
        'command' => ['make', 'mk'],
        'action' => ['middleware', 'guard', '-g'],
        'alias' => '-g',
        'info'  => 'Http Middleware or Guard which filter requests to access certain functionalities',
        'callback' => [Callback::class, 'middleware']
    ],
    [
        'command' => ['make', 'mk'],
        'action' => ['kernel', 'provider', '-k'],
        'alias' => '-k',
        'info'  => 'Application Kernel which setup all the required functionalities before boot',
        'callback' => [Callback::class, 'kernel']
    ],
    [
        'command' => ['make', 'mk'],
        'action' => ['resource', '-rs'],
        'alias' => '-rs',
        'info'  => 'Create Application Resource Pack (Model, View and Controller)',
        'callback' => [Callback::class, 'resource']
    ],
    [
        'command' => ['make', 'mk'],
        'action' => ['view', 'vw', '-v'],
        'alias' => '-v',
        'info'  => 'Create a new Application View file',
        'callback' => [Callback::class, 'view']
    ],
    [
        'command' => ['make', 'mk'],
        'action' => ['mail', '-ml'],
        'alias' => '-ml',
        'info'  => 'Create a Mail Template to send Emails from Application',
        'callback' => [Callback::class, 'mail']
    ],
    [
        'command' => 'migrate',
        'info'  => 'Migrate all the existing database schema to create table into database',
        'callback' => [Callback::class, 'migrate']
    ],
    [
        'command' => 'rollback',
        'info'  => 'Rollback Last Migrated Database Schema and Run Drop command',
        'callback' => [Callback::class, 'rollback']
    ],
    [
        'command' => 'seed',
        'info'  => 'Seed all the existing seeder files to create dummy data into database',
        'callback' => [Callback::class, 'seed']
    ],
];
