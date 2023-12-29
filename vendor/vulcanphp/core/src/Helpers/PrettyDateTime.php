<?php

namespace VulcanPhp\Core\Helpers;

class PrettyDateTime
{
    // The constants correspond to units of time in seconds
    const MINUTE = 60;
    const HOUR   = 3600;
    const DAY    = 86400;
    const WEEK   = 604800;
    const MONTH  = 2628000;
    const YEAR   = 31536000;

    private static function prettyFormat($difference, $unit)
    {
        // $prepend is added to the start of the string if the supplied
        // difference is greater than 0, and $append if less than
        $prepend = ($difference < 0) ? 'In ' : '';
        $append  = ($difference > 0) ? ' ago' : '';

        $difference = floor(abs($difference));

        // If difference is plural, add an 's' to $unit
        if ($difference > 1) {
            $unit = $unit . 's';
        }

        return str_replace('#', $difference, self::translate(sprintf('%s%s %s%s', $prepend, '#', $unit, $append)));
    }

    public static function parse(\DateTime $dateTime, \DateTime $reference = null)
    {
        // If not provided, set $reference to the current DateTime
        if (!$reference) {
            $reference = new \DateTime('now', new \DateTimeZone($dateTime->getTimezone()->getName()));
        }

        // Get the difference between the current date and the supplied $dateTime
        $difference = $reference->format('U') - $dateTime->format('U');
        $absDiff    = abs($difference);

        // Get the date corresponding to the $dateTime
        $date = $dateTime->format('Y/m/d');

        // Throw exception if the difference is NaN
        if (is_nan($difference)) {
            throw new \Exception('The difference between the DateTimes is NaN.');
        }

        // Today
        if ($reference->format('Y/m/d') == $date) {
            if (0 <= $difference && $absDiff < self::MINUTE) {
                return self::translate('Moments ago');
            } elseif ($difference < 0 && $absDiff < self::MINUTE) {
                return self::translate('Seconds from now');
            } elseif ($absDiff < self::HOUR) {
                return self::prettyFormat($difference / self::MINUTE, 'minute');
            } else {
                return self::prettyFormat($difference / self::HOUR, 'hour');
            }
        }

        $yesterday = clone $reference;
        $yesterday->modify('- 1 day');

        $tomorrow = clone $reference;
        $tomorrow->modify('+ 1 day');

        if ($yesterday->format('Y/m/d') == $date) {
            return self::translate('Yesterday');
        } else if ($tomorrow->format('Y/m/d') == $date) {
            return self::translate('Tomorrow');
        } else if ($absDiff / self::DAY <= 7) {
            return self::prettyFormat($difference / self::DAY, 'day');
        } else if ($absDiff / self::WEEK <= 5) {
            return self::prettyFormat($difference / self::WEEK, 'week');
        } else if ($absDiff / self::MONTH < 12) {
            return self::prettyFormat($difference / self::MONTH, 'month');
        }

        // Over a year ago
        return self::prettyFormat($difference / self::YEAR, 'year');
    }

    protected static function translate(string $text = ''): string
    {
        if (function_exists('translate')) {
            return translate($text);
        }

        return $text;
    }
}
