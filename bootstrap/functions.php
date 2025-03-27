<?php

use Lib\TailwindHelper;

/**
 * Retrieves an instance of the TailwindHelper class.
 *
 * This function returns the TailwindHelper instance from the dependency 
 * injection container, allowing the caller to use its methods for 
 * generating Tailwind CSS classes and configurations.
 *
 * @return TailwindHelper The TailwindHelper instance.
 */
function tailwind(): TailwindHelper
{
    return get(TailwindHelper::class);
}

/**
 * Converts a given string into a prettier text by replacing underscores, dashes, and dots
 * with spaces and capitalizing the first letter of each word.
 *
 * @param string $text The input string to be prettified.
 *
 * @return string The prettified version of the input string.
 */
function pretty_text(string $text): string
{
    return trim(ucwords(str_replace(['_', '-', '.'], ' ', $text)));
}

/**
 * Converts a string into a URL-friendly "slug" by normalizing,
 * transliterating, and formatting it.
 *
 * The function normalizes the string to NFKD form, transliterates
 * it to ASCII, converts it to lowercase, and replaces non-alphanumeric
 * characters with a specified separator. It also removes leading and
 * trailing separators.
 *
 * @param ?string $string The input string to be slugified.
 * @param string $separator The separator to use for non-alphanumeric characters.
 *
 * @return string The slugified version of the input string.
 */
function slugify(?string $string, string $separator = '-'): string
{
    $string ??= '';

    // Normalize the string (NFKD normalization)
    $string = normalizer_normalize($string, Normalizer::FORM_KD);

    // Transliterate the string to ASCII
    $string = transliterator_transliterate('Any-Latin; Latin-ASCII', $string);

    // Convert the string to lowercase
    $string = mb_strtolower($string, 'UTF-8');

    // Replace any non-alphanumeric characters with the separator
    $string = preg_replace('/[^a-z0-9]+/u', $separator, $string);

    // Remove any leading or trailing separators
    $string = trim($string, $separator);

    return $string;
}

/**
 * Renders the 404 error template and sends the response.
 *
 * This function renders the 404 error template and sends the response with
 * a 404 status code. It also calls exit to prevent any further code execution.
 * 
 * @return void
 */
function page_not_found(): void
{
    fireline('errors/404')
        ->setStatusCode(404)
        ->send();
    exit;
}

/**
 * Trim a string to a specified number of words.
 *
 * @param string $string The input string.
 * @param int $wordLimit The maximum number of words to keep.
 * @param string $suffix A suffix to append if the string is trimmed (default: '...').
 * @param bool $trimFromMiddle Whether to trim from the middle of the string (default: false).
 * @return string The trimmed string.
 */
function trim_words(string $string, int $wordLimit = 10, string $suffix = '...', bool $trimFromMiddle = false): string
{
    $words = explode(' ', $string);

    if (count($words) <= $wordLimit) {
        return $string; // No trimming needed.
    }

    if ($trimFromMiddle) {
        // Calculate words to keep from the start and the end.
        $halfLimit = (int) floor($wordLimit / 2);
        $startWords = array_slice($words, 0, $halfLimit);
        $endWords = array_slice($words, -$halfLimit);

        // Adjust for odd word limits.
        if ($wordLimit % 2 !== 0) {
            $startWords[] = $words[$halfLimit];
        }

        return implode(' ', $startWords) . " $suffix " . implode(' ', $endWords);
    }

    // Default: Trim from the start.
    return implode(' ', array_slice($words, 0, $wordLimit)) . $suffix;
}

/**
 * Checks if a feature is enabled.
 *
 * @param string $feature The name of the feature to check.
 * @return bool True if the feature is enabled, false otherwise.
 */
function is_feature_enabled(string $feature): bool
{
    $features = env('cms', [])['features'] ?? [];
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
    $cms = env('cms', []);
    return $cms[$key] ?? $default;
}
