<?php

return [
    // application local timezone
    'timezone' => 'Asia/Dhaka',

    // enable/disable development mode
    'development' => false,

    // default application display language
    'language' => 'en',

    // application storage directory path
    'storage_dir' => root_dir('/storage'),

    // resource directory path
    'resource_dir' => root_dir('/resources'),

    // language directory path
    'language_dir' => root_dir('/storage/local'),

    // vite app directory path
    'vite_dir' => root_dir('/resources/vite'),

    // temporary directory path
    'tmp_dir' => root_dir('/storage/tmp'),

    // application version
    'version' => 'v1.0.0'
];
