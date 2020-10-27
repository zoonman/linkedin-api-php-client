<?php

namespace Pricat\Utils;

class Helper
{
    const UNIT_BYTES = 1;
    const UNIT_KIBIBYTES = 2;
    const UNIT_MEBIBYTES = 3;

    public static $unit = self::UNIT_BYTES;
    public static $timerStart = null;

    public static function getNumberFormatted($value, $precision = 2)
    {
        $value = self::getFloatFormatted($value, $precision);
        if ($precision > 0) {
            if ((int)$value == $value) {
                $value = number_format((float)$value, 0);
            }
        }
        return $value;
    }

    public static function getFloatFormatted($value, $precision = 2)
    {
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);
        return number_format((float)$value, $precision, '.', '');
    }

    public static function printStats($title)
    {
        if (is_null(self::$timerStart)) {
            self::$timerStart = microtime(true);
        }

        self::printInfo(sprintf("\n%s\n%s\n%s\n",
            $title,
            sprintf('- Memory usage: %s', self::memory_get_usage_human()),
            sprintf('- Execute time: %.6f seconds', microtime(true) - self::$timerStart)
        ));

        return microtime(true) - self::$timerStart;
    }

    public static function printInfo($string)
    {
        if (USERMODE) {
            echo $string . '<br/>';
        }
        error_log($string, 3, LOG_FILE);
    }

    public static function memory_get_usage_human()
    {
        $mem = memory_get_usage();

        if (self::$unit === self::UNIT_KIBIBYTES) {
            return sprintf('%.4f KiB', $mem / 1024.0);
        } else if (self::$unit === self::UNIT_MEBIBYTES) {
            return sprintf('%.4f MiB', $mem / 1024.0 / 1024.0);
        }
        return sprintf('%d bytes', $mem);
    }

    public static function printDump($var)
    {
        ob_start();
        var_dump($var);
        $contents = ob_get_contents();
        ob_end_clean();

        self::printDebug(sprintf("\n%s", $contents));
    }

    public static function printDebug($string)
    {
        if (DEBUG_MODE) {
            if (USERMODE) {
                echo $string . '<br/>';
            }
            error_log($string, 3, LOG_FILE);
        }
    }
}
