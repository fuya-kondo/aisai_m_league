<?php

/**
 * 共通ヘルパー関数群
 */

/**
 * 指定キーと値に一致する最初の要素を返す
 *
 * @param array $list
 * @param string $key
 * @param mixed $value
 * @return array|null
 */
function findFirstByKey(array $list, string $key, $value): ?array
{
    foreach ($list as $item) {
        if (isset($item[$key]) && $item[$key] === $value) {
            return $item;
        }
    }
    return null;
}

/**
 * 配列を指定キーで連想配列化して返す
 *
 * @param array $list
 * @param string $key
 * @return array
 */
function indexByKey(array $list, string $key): array
{
    $result = [];
    foreach ($list as $item) {
        if (isset($item[$key])) {
            $result[$item[$key]] = $item;
        }
    }
    return $result;
}

?>

