<?php
namespace App\Services;

use RuntimeException;
use Throwable;

/**
 * Gemini へのテキスト生成リクエストを共通化する service。
 * 用途ごとの差分は model / prompt / generationConfig で切り替える。
 */
final class GeminiTextGenerationService
{
    public function generateText(string $prompt, string $model, ?string &$errorMessage = null, array $generationConfig = []): ?string
    {
        $apiKey = getenv('GEMINI_API_KEY') ?: '';
        if ($apiKey === '') {
            $errorMessage = 'GEMINI_API_KEY が設定されていません。config/.env を確認してください。';
            return null;
        }

        $url = sprintf(
            'https://generativelanguage.googleapis.com/v1/models/%s:generateContent?key=%s',
            rawurlencode($model),
            rawurlencode($apiKey)
        );

        $requestData = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
        ];

        if (!empty($generationConfig)) {
            $requestData['generationConfig'] = $generationConfig;
        }

        try {
            $requestOptions = [
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-Type: application/json\r\n",
                    'content' => json_encode($requestData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'ignore_errors' => true,
                    'timeout' => 30,
                ],
            ];
            $context = stream_context_create($requestOptions);
            $response = file_get_contents($url, false, $context);
            if ($response === false) {
                throw new RuntimeException('AI へのリクエスト送信に失敗しました。');
            }

            $decodedResponse = json_decode($response, true);
            if (isset($decodedResponse['error']['message'])) {
                throw new RuntimeException((string)$decodedResponse['error']['message']);
            }

            $text = $decodedResponse['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if (!is_string($text) || trim($text) === '') {
                $errorMessage = 'AI から有効な応答を取得できませんでした。';
                return null;
            }

            return trim($text);
        } catch (Throwable $throwable) {
            $errorMessage = 'エラーが発生しました: ' . $throwable->getMessage();
            return null;
        }
    }
}
