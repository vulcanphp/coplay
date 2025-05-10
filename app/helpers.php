<?php

use App\Lib\TailwindHelper;

/**
 * Checks if a feature is enabled.
 *
 * @param string $feature The name of the feature to check.
 * @return bool True if the feature is enabled, false otherwise.
 */
function is_feature_enabled(string $feature): bool
{
    $features = config('cms.features', []);
    return isset($features[$feature]) && $features[$feature] === true;
}

/**
 * Retrieves a value from the 'cms' array in the environment.
 *
 * @param string $key The key to retrieve.
 * @param mixed $default The default value to return if the key is not set.
 * @return mixed The retrieved value or the default value if the key is not set.
 */
function cms(string $key, $default = null)
{
    $cms = config('cms', []);
    return $cms[$key] ?? $default;
}

/**
 * Return the Tailwind Css Helper Class from Container.
 * 
 * @return \App\Lib\TailwindHelper
 */
function tailwind()
{
    return get(TailwindHelper::class);
}