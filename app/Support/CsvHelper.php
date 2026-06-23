<?php

namespace App\Support;

class CsvHelper
{
    public static function countDataRows(string $path, ?int $minimum = null): int
    {
        $handle = fopen($path, 'r');

        if ($handle === false) {
            return 0;
        }

        $rows = 0;

        while (fgets($handle) !== false) {
            $rows++;

            if ($minimum !== null && $rows - 1 >= $minimum) {
                break;
            }
        }

        fclose($handle);

        return max(0, $rows - 1);
    }

    public static function hasMinimumRows(string $path, int $minimum): bool
    {
        return self::countDataRows($path, $minimum) >= $minimum;
    }
}
