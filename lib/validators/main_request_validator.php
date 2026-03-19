<?php
/**
 * main 画面のフォーム入力を整理する補助関数群。
 */

function mainFindMissingFields(array $source, array $requiredFields): array
{
    return array_values(array_filter($requiredFields, static function ($field) use ($source) {
        return !isset($source[$field]) || trim((string)$source[$field]) === '';
    }));
}

function mainBuildPostedDateTime(array $source, string $yearKey = 'year', string $monthKey = 'month', string $dayKey = 'day'): string
{
    return sprintf(
        '%04d-%02d-%02d %s',
        (int)($source[$yearKey] ?? 0),
        (int)($source[$monthKey] ?? 0),
        (int)($source[$dayKey] ?? 0),
        date('H:i:s')
    );
}

function mainReadMistakeCount(array $source, string $key = 'mistake_count'): int
{
    $value = trim((string)($source[$key] ?? '0'));
    if ($value === '') {
        return 0;
    }
    if (!preg_match('/^\d+$/', $value)) {
        return 0;
    }
    return (int)$value;
}
