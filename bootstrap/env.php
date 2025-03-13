<?php

/**
 * This file contains the environment settings for debugging, directories, API keys, database connections, and mail server configurations.
 * Each configuration key is associated with a value that can be used to customize the application.
 *
 * @return array
 *     An associative array of settings.
 */

return [
    // Debugging settings
    'debug' => false,

    // Directory paths
    'storage_dir' => __DIR__ . '/../storage', // Storage directory
    'cache_dir' => __DIR__ . '/../storage/cache', // Cache files directory
    'upload_dir' => __DIR__ . '/../public/uploads', // Upload directory
    'template_dir' => __DIR__ . '/../app/Templates', // Template directory
    'lang_dir' => __DIR__ . '/../i18n', // Language files directory

    // URL settings
    'media_url' => '/uploads/', // Media URL
    'asset_url' => '/resources/', // Asset URL

    // Localization settings
    'lang' => 'en', // Default language

    // Security settings
    'app_key' => '4c5d44fb54e7830cb7b7f455514e408124f9f9372f071a9eeba2797386767bc2', // Application key for encryption and authentication

    // Database connection settings
    'database' => [
        'driver' => 'sqlite', // Database driver
        'file' => __DIR__ . '/../storage/sqlite.db', // SQLite Database filepath 
    ],

    // TMDB API key
    'TMDB_API_KEY' => '{PAST_YOUR_TMDB_API_HERE}', // Get your TMDB API key from https://www.themoviedb.org

    // Site general settings
    'cms' => [
        'title' => 'CoPlay', // Site title
        'tagline' => 'Stream Free Movies & TV Series Online', // Site tagline
        'intro' => 'Welcome to CoPlay, Free Streaming Platform', // Site intro
        'description' => 'Stream Free Movies, TV Series, Anime, and Drama Online with HD Quality. Watch Anywhere Anytime in CoPlay.', // Site description
        'disclaimer' => 'We does not host any files, it merely links to 3rd party services. CoPlay is not responsible for any media files shown by the video providers', // Site disclaimer notice
        'copyright' => '&copy; 2025 all right reserved.', // Site copyright text

        // Color settings
        'color' => [
            // available colors: https://tailwindcss.com/docs/colors
            'primary' => 'gray', // Primary color (Background and Text)
            'accent' => 'amber', // Accent/Brand color
        ],

        // Site Features
        'features' => [
            'auto_embed' => true, // Enable Auto Embeds support
            'auto_embed_update' => true, // Enable Auto Embeds Update
            'api' => true, // Enable Public API support
        ],
    ],
];
