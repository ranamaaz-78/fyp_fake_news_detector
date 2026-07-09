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

    /**
     * Candidate header names for the free-text column, in priority order.
     *
     * @var list<string>
     */
    private const TEXT_COLUMNS = ['text', 'content', 'article', 'body', 'statement', 'news', 'title'];

    /**
     * Candidate header names for the label column, in priority order.
     *
     * @var list<string>
     */
    private const LABEL_COLUMNS = ['label', 'class', 'target', 'category', 'type'];

    private const FAKE_VALUES = ['fake', 'false', 'f', '0', 'fabricated', 'unreliable', 'fake_news'];

    private const REAL_VALUES = ['real', 'true', 't', '1', 'reliable', 'factual', 'real_news'];

    /**
     * Inspect a combined dataset CSV (text + label columns) and report whether it can
     * be used for training, along with per-class row counts.
     *
     * @return array{valid: bool, text_column: ?string, label_column: ?string, real: int, fake: int, total: int, reason: ?string}
     */
    public static function analyzeDataset(string $path, int $minPerClass = 100): array
    {
        $result = [
            'valid' => false,
            'text_column' => null,
            'label_column' => null,
            'real' => 0,
            'fake' => 0,
            'total' => 0,
            'reason' => null,
        ];

        $handle = fopen($path, 'r');

        if ($handle === false) {
            $result['reason'] = 'File could not be opened.';

            return $result;
        }

        $header = fgetcsv($handle);

        if ($header === false || $header === null) {
            fclose($handle);
            $result['reason'] = 'File is empty or missing a header row.';

            return $result;
        }

        $textIndex = self::locateColumn($header, self::TEXT_COLUMNS);
        $labelIndex = self::locateColumn($header, self::LABEL_COLUMNS);

        if ($textIndex === null) {
            fclose($handle);
            $result['reason'] = 'No text column found. Expected one of: '.implode(', ', self::TEXT_COLUMNS).'.';

            return $result;
        }

        if ($labelIndex === null) {
            fclose($handle);
            $result['reason'] = 'No label column found. Expected one of: '.implode(', ', self::LABEL_COLUMNS).'.';

            return $result;
        }

        $result['text_column'] = $header[$textIndex];
        $result['label_column'] = $header[$labelIndex];

        while (($row = fgetcsv($handle)) !== false) {
            if (! isset($row[$textIndex], $row[$labelIndex])) {
                continue;
            }

            $text = trim((string) $row[$textIndex]);

            if ($text === '') {
                continue;
            }

            $class = self::normalizeLabel($row[$labelIndex]);

            if ($class === 'FAKE') {
                $result['fake']++;
                $result['total']++;
            } elseif ($class === 'REAL') {
                $result['real']++;
                $result['total']++;
            }
        }

        fclose($handle);

        if ($result['fake'] < $minPerClass || $result['real'] < $minPerClass) {
            $result['reason'] = "Each class needs at least {$minPerClass} labelled rows (found {$result['real']} REAL, {$result['fake']} FAKE).";

            return $result;
        }

        $result['valid'] = true;

        return $result;
    }

    /**
     * Split a combined dataset CSV into Kaggle-style Fake.csv / True.csv files
     * (each with a single "text" column) so it can feed the existing trainer.
     *
     * @return array{fake: int, real: int}
     */
    public static function splitByLabel(string $sourcePath, string $fakeDest, string $trueDest): array
    {
        $source = fopen($sourcePath, 'r');

        if ($source === false) {
            throw new \RuntimeException('Could not read the dataset file.');
        }

        $header = fgetcsv($source);

        if ($header === false || $header === null) {
            fclose($source);
            throw new \RuntimeException('Dataset file has no header row.');
        }

        $textIndex = self::locateColumn($header, self::TEXT_COLUMNS);
        $labelIndex = self::locateColumn($header, self::LABEL_COLUMNS);

        if ($textIndex === null || $labelIndex === null) {
            fclose($source);
            throw new \RuntimeException('Dataset must contain a text column and a label column.');
        }

        $fakeHandle = fopen($fakeDest, 'w');
        $trueHandle = fopen($trueDest, 'w');

        if ($fakeHandle === false || $trueHandle === false) {
            fclose($source);
            throw new \RuntimeException('Could not create split dataset files.');
        }

        fputcsv($fakeHandle, ['text']);
        fputcsv($trueHandle, ['text']);

        $counts = ['fake' => 0, 'real' => 0];

        while (($row = fgetcsv($source)) !== false) {
            if (! isset($row[$textIndex], $row[$labelIndex])) {
                continue;
            }

            $text = trim((string) $row[$textIndex]);

            if ($text === '') {
                continue;
            }

            $class = self::normalizeLabel($row[$labelIndex]);

            if ($class === 'FAKE') {
                fputcsv($fakeHandle, [$text]);
                $counts['fake']++;
            } elseif ($class === 'REAL') {
                fputcsv($trueHandle, [$text]);
                $counts['real']++;
            }
        }

        fclose($source);
        fclose($fakeHandle);
        fclose($trueHandle);

        return $counts;
    }

    /**
     * @param  list<string>  $header
     * @param  list<string>  $candidates
     */
    private static function locateColumn(array $header, array $candidates): ?int
    {
        $normalized = array_map(
            static fn ($name) => strtolower(trim((string) $name)),
            $header,
        );

        foreach ($candidates as $candidate) {
            $index = array_search($candidate, $normalized, true);

            if ($index !== false) {
                return (int) $index;
            }
        }

        return null;
    }

    private static function normalizeLabel(string $value): ?string
    {
        $value = strtolower(trim($value));

        if (in_array($value, self::FAKE_VALUES, true)) {
            return 'FAKE';
        }

        if (in_array($value, self::REAL_VALUES, true)) {
            return 'REAL';
        }

        return null;
    }
}
