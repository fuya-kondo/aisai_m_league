<?php
/**
 * 管理画面の POST 入力を最小限の規則で正規化・検証する補助関数群。
 */

function adminEnsurePostRequest(string $requestMethod): void
{
    if ($requestMethod !== 'POST') {
        throw new InvalidArgumentException('不正なリクエストです');
    }
}

function adminReadRequiredString(array $source, string $key, string $label): string
{
    $value = trim((string)($source[$key] ?? ''));
    if ($value === '') {
        throw new InvalidArgumentException($label . 'は必須です');
    }
    return $value;
}

function adminReadOptionalString(array $source, string $key, string $default = ''): string
{
    return trim((string)($source[$key] ?? $default));
}

function adminReadOptionalInt(array $source, string $key, int $default = 0): int
{
    $value = trim((string)($source[$key] ?? ''));
    if ($value === '') {
        return $default;
    }
    if (!preg_match('/^-?\d+$/', $value)) {
        throw new InvalidArgumentException($key . ' の値が不正です');
    }
    return (int)$value;
}

function adminReadRequiredInt(array $source, string $key, string $label, ?int $min = null, ?int $max = null): int
{
    $value = adminReadRequiredString($source, $key, $label);
    if (!preg_match('/^-?\d+$/', $value)) {
        throw new InvalidArgumentException($label . 'は整数で入力してください');
    }

    $number = (int)$value;
    if ($min !== null && $number < $min) {
        throw new InvalidArgumentException($label . 'は' . $min . '以上で入力してください');
    }
    if ($max !== null && $number > $max) {
        throw new InvalidArgumentException($label . 'は' . $max . '以下で入力してください');
    }

    return $number;
}

function adminDecodePostedJson(array $source, string $key = 'data'): array
{
    $json = adminReadRequiredString($source, $key, $key);
    $decoded = json_decode($json, true);
    if (!is_array($decoded)) {
        throw new InvalidArgumentException('送信データの形式が不正です');
    }
    return $decoded;
}

function adminBuildPlayDateTime(array $source, string $dateKey = 'play_date', string $timeKey = 'play_time'): string
{
    $playDate = adminReadRequiredString($source, $dateKey, 'プレイ日');
    $playTime = adminReadOptionalString($source, $timeKey, '00:00');
    return $playDate . ' ' . ($playTime !== '' ? $playTime : '00:00') . ':00';
}
