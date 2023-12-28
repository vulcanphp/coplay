<?php

namespace VulcanPhp\Core\Helpers;

use DateTime;
use DateTimeZone;

class Time
{
    public const
        DEFAULT_DATE_FORMAT         = 'd M, Y',
        DEFAULT_TIME_FORMAT         = 'g:i a',
        DEFAULT_DATE_TIME_FORMAT    = self::DEFAULT_DATE_FORMAT . ', ' . self::DEFAULT_TIME_FORMAT;

    public static function format($date = 'now', string $format = null): string
    {
        if ($format === null) {
            $format = self::DEFAULT_DATE_TIME_FORMAT;
        }

        if (intval($date) == $date) {
            return date($format, $date);
        }

        return date($format, strtotime($date));
    }

    public static function dateFormat($date,  ?string $format = null): string
    {
        if ($format === null) {
            $format = self::DEFAULT_DATE_FORMAT;
        }

        return date($format, strtotime($date));
    }

    public static function timeFormat($date,  ?string $format = null): string
    {
        if ($format === null) {
            $format = self::DEFAULT_TIME_FORMAT;
        }

        return date($format, strtotime($date));
    }

    public static function getReadTime(string $content, int $wpm = 180): string
    {
        $word   = str_word_count(strip_tags($content));
        $minute = floor($word / $wpm);
        $second = floor($word % $wpm / ($wpm / 60));

        return ($minute > 0 ? $minute . ' minute' . ($minute == 1 ? '' : 's') . ', ' : '') .  ($second > 0 ? $second . ' second' . ($second == 1 ? '' : 's') : '');
    }

    public static function getDateTime(?int $timestamp = null): string
    {
        if (null === $timestamp) {
            $timestamp = time();
        }

        return date('Y-m-d H:i:s', $timestamp);
    }

    public static function isLeapYear(int $year = null): bool
    {
        if (null === $year) {
            $year = date('Y');
        }

        return $year % 4 == 0 && ($year % 100 != 0 || $year % 400 == 0);
    }

    public static function isTimezone(string $timezone): bool
    {
        return in_array($timezone, DateTimeZone::listIdentifiers());
    }

    public static function isFormat(string $date, string $format, $strict = true): bool
    {
        $dateTime = DateTime::createFromFormat($format, $date);

        if ($strict) {
            $errors = DateTime::getLastErrors();
            if (!empty($errors['warning_count'])) {
                return false;
            }
        }

        return $dateTime !== false;
    }

    public static function inPast(string $date): bool
    {
        return strtotime($date) < time();
    }

    public static function inFuture(string $date): bool
    {
        return strtotime($date) > time();
    }

    public static function isBefore(string $date, string $before): bool
    {
        return strtotime($date) < strtotime($before);
    }

    public static function isAfter(string $date, string $after): bool
    {
        return strtotime($date) > strtotime($after);
    }

    public static function stopwatch(callable $callback, int $times = 1, int $decimals = 5): float
    {
        $start = microtime(true);
        $i     = 0;
        while ($i < $times) {
            $i++;
            $callback();
        }

        $end = microtime(true);
        return round($end - $start, $decimals);
    }
}
