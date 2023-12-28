<?php

namespace VulcanPhp\Core\Helpers;

class Str
{
    public static function contains($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '') {
                if (\function_exists('mb_strpos')) {
                    if (\mb_strpos($haystack, $needle) !== false) {
                        return true;
                    }
                } elseif (\strpos($haystack, $needle) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function startsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '') {
                if (\function_exists('mb_strpos')) {
                    if (\mb_strpos($haystack, $needle) === 0) {
                        return true;
                    }
                } elseif (\strpos($haystack, $needle) === 0) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function hasSpace(string $string): bool
    {
        return strpos($string, ' ') !== false;
    }

    public static function replace($find, $replace,  ?string $string = null,  ?int $count = null): ?string
    {
        if ($string === null) {
            return null;
        }

        return str_replace($find, $replace, $string, $count);
    }

    public static function after(string $after, string $string): ?string
    {
        return substr($string, strpos($string, $after));
    }

    public static function endsWith(string $string, string $ends_with = ''): bool
    {
        $length = strlen($ends_with);
        return $length === 0 || (substr($string, -$length) === $ends_with);
    }

    public static function startWith(string $string, string $start_with = ''): string
    {
        if (!self::startsWith($string, $start_with)) {
            return $start_with . $string;
        }

        return $string;
    }

    public static function endWith(string $string, string $end_with = ''): string
    {
        if (!self::endsWith($string, $end_with)) {
            return $string . $end_with;
        }

        return $string;
    }

    public static function lowercase(string $string, string $encoding = 'UTF-8'): string
    {
        return mb_convert_case($string, MB_CASE_LOWER, $encoding);
    }

    public static function uppercase(string $string, string $encoding = 'UTF-8'): string
    {
        return mb_convert_case($string, MB_CASE_UPPER, $encoding);
    }

    public static function titleCase(string $string, string $encoding = 'UTF-8'): string
    {
        return mb_convert_case($string, MB_CASE_TITLE, $encoding);
    }

    public static function camelCase(string $string): string
    {
        // Non-alpha and non-numeric characters become spaces
        $string = preg_replace("/[^a-z0-9]+/i", " ", $string);
        $string = ucwords(strtolower(trim($string)));

        return lcfirst(str_replace(" ", "", $string));
    }

    public static function slug(string $string, bool $lowercase = true): string
    {
        $string = str_replace(["'", '"', '&'], ['-', '-', 'and'], html_entity_decode($string));
        // Replace non letter or digit with hyphen (-)
        $string = preg_replace('/[^a-z0-9]+/i', '-', $string);
        // Transliterate
        $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);
        // Trim
        $string = trim($string, '-');
        // Remove duplicate -
        $string = preg_replace('/-+/', '-', $string);
        // lowercase slug
        $string = $lowercase === true ? self::lowercase($string) : $string;

        return $string;
    }

    public static function slugif(?string $text = null): ?string
    {
        if (strlen($text) != mb_strlen($text, 'utf-8')) {
            return str_replace([' ', '_', ',', '@', '#', '&', '(', ')', '!', '*', '$', '%', ';', '{', '}', '|', '/', '.'], '-', $text);
        }

        return self::slug($text);
    }

    public static function snakeCase(string $string, bool $lowercase = true): string
    {
        // Replace non letter or digit with underscore (_)
        $string = preg_replace('/[^a-z0-9]+/i', '_', $string);
        // Transliterate
        $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);
        // Trim
        $string = trim($string, '_');
        // Remove duplicate _
        $string = preg_replace('/_+/', '_', $string);

        if (true === $lowercase) {
            return self::lowercase($string);
        }

        return $string;
    }

    public static function limit(string $string, int $limit = 100, string $end = '..'): string
    {
        if (mb_strwidth($string, 'UTF-8') <= $limit) {
            return $string;
        }

        return rtrim(mb_strimwidth($string, 0, $limit, '', 'UTF-8')) . $end;
    }

    public static function limitWords(string $string, int $limit = 10, string $end = '...'): string
    {
        $arrayWords = explode(' ', $string);

        if (sizeof($arrayWords) <= $limit) {
            return $string;
        }

        return implode(' ', array_slice($arrayWords, 0, $limit)) . $end;
    }

    public static function read(string $string = ''): string
    {
        return self::titleCase(preg_replace('/[-_\/]+/i', ' ', $string));
    }

    public static function uuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    public static function pure(?string $text = null): ?string
    {
        if ($text !== null) {
            return preg_replace("/[^a-zA-Z0-9]+/", "", $text);
        }

        return null;
    }
}
