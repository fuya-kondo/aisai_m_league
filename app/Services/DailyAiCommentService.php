<?php
namespace App\Services;

use App\Repositories\DailyAiCommentRepository;
use App\Support\Constants\AppConstants;
use Throwable;

/**
 * 本日成績向けの短い AI コメント生成・保存を扱う service。
 */
final class DailyAiCommentService
{
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';
    public const COMMENT_MODEL = 'gemini-2.5-flash-lite';

    public function __construct(
        private readonly DailyAiCommentRepository $repository,
        private readonly GeminiTextGenerationService $geminiService,
    ) {
    }

    public function findByGame(int $tableId, string $playDate, int $game): ?array
    {
        try {
            $record = $this->repository->findByTableDateGame($tableId, $playDate, $game);
        } catch (Throwable $throwable) {
            error_log('日次AIコメント取得エラー: ' . $throwable->getMessage());
            return null;
        }

        if ($record === null) {
            return null;
        }

        $comments = [];
        if (!empty($record['response_json'])) {
            $decoded = json_decode((string)$record['response_json'], true);
            if (is_array($decoded)) {
                $comments = $decoded;
            }
        }

        return [
            'status' => (string)($record['status'] ?? ''),
            'comments' => $comments,
            'errorMessage' => (string)($record['error_message'] ?? ''),
            'game' => (int)($record['game'] ?? 0),
            'playDate' => (string)($record['play_date'] ?? ''),
        ];
    }

    public function generateAndSaveForGame(\StatsService $statsService, string $playDate, int $tableId, int $game): bool
    {
        $source = $statsService->buildDailyAiCommentSource($playDate, $tableId, $game);
        if ($source === null) {
            return false;
        }

        return $this->generateAndSaveFromSource($source);
    }

    public function rebuildForDateFromGame(\StatsService $statsService, string $playDate, int $tableId, int $fromGame): void
    {
        try {
            $this->repository->deleteByTableDateFromGame($tableId, $playDate, $fromGame);
            $gameNumbers = $statsService->getDailyGameNumbers($playDate, $tableId);
            sort($gameNumbers, SORT_NUMERIC);

            foreach ($gameNumbers as $gameNumber) {
                if ($gameNumber < $fromGame) {
                    continue;
                }

                $source = $statsService->buildDailyAiCommentSource($playDate, $tableId, $gameNumber);
                if ($source === null) {
                    break;
                }

                $this->generateAndSaveFromSource($source);
            }
        } catch (Throwable $throwable) {
            error_log('日次AIコメント再生成エラー: ' . $throwable->getMessage());
        }
    }

    private function generateAndSaveFromSource(array $source): bool
    {
        $prompt = $this->buildPrompt($source);
        $errorMessage = null;
        $responseText = $this->geminiService->generateText(
            $prompt,
            self::COMMENT_MODEL,
            $errorMessage
        );

        if ($responseText === null) {
            $this->saveFailure($source, $errorMessage ?? 'AI コメントの取得に失敗しました。');
            return false;
        }

        $expectedUserIds = array_map(
            static fn(array $player): int => (int)$player['user_id'],
            $source['players'] ?? []
        );
        $comments = $this->parseComments($responseText, $expectedUserIds);
        if ($comments === null) {
            $this->saveFailure($source, 'AI コメントの応答形式が不正でした。');
            return false;
        }

        try {
            return $this->repository->upsert([
                'play_date' => $source['play_date'],
                'game' => $source['game'],
                'u_table_id' => $source['table_id'],
                'model_name' => self::COMMENT_MODEL,
                'status' => self::STATUS_SUCCESS,
                'response_json' => json_encode($comments, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'error_message' => null,
                'generated_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (Throwable $throwable) {
            error_log('日次AIコメント保存エラー: ' . $throwable->getMessage());
            return false;
        }
    }

    private function saveFailure(array $source, string $errorMessage): void
    {
        try {
            $this->repository->upsert([
                'play_date' => $source['play_date'],
                'game' => $source['game'],
                'u_table_id' => $source['table_id'],
                'model_name' => self::COMMENT_MODEL,
                'status' => self::STATUS_FAILED,
                'response_json' => null,
                'error_message' => $errorMessage,
                'generated_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (Throwable $throwable) {
            error_log('日次AIコメント失敗保存エラー: ' . $throwable->getMessage());
        }
    }

    private function buildPrompt(array $source): string
    {
        $lines = [];
        $lines[] = 'あなたは麻雀成績アプリ向けの簡易コメント生成AIです。';
        $lines[] = '今日ここまでの成績を見て、各プレイヤーに短い一言コメントを返してください。';
        $lines[] = '出力はJSONのみで返してください。Markdownや説明文は不要です。';
        $lines[] = '形式: {"comments":[{"user_id":1,"comment":"..."},{"user_id":2,"comment":"..."},{"user_id":3,"comment":"..."},{"user_id":4,"comment":"..."}]}';
        $lines[] = '条件:';
        $lines[] = '- comment は各プレイヤー1件、40〜80文字程度の日本語';
        $lines[] = '- 今日の成績だけを見る';
        $lines[] = '- 2半荘目以降は今日の過去成績も踏まえる';
        $lines[] = '- 良い点か課題を一言で具体的に触れる';
        $lines[] = '- 断定しすぎず、軽い語調にする';
        $lines[] = '- HTMLは使わない';
        $lines[] = '';
        $lines[] = sprintf('対象日: %s', $source['play_date']);
        $lines[] = sprintf('対象半荘: %d半荘目', $source['game']);
        $lines[] = sprintf('本日完了半荘数: %d', (int)($source['completed_games'] ?? 0));
        $lines[] = '';

        foreach ($source['players'] as $player) {
            $lines[] = '---';
            $lines[] = sprintf('user_id: %d', (int)$player['user_id']);
            $lines[] = sprintf('選手名: %s', $player['player_name']);
            $lines[] = sprintf('現在の席: %s', $player['direction']);
            $lines[] = sprintf(
                '今回結果: %s着 / 素点:%d / pt:%s / チョンボ:%d',
                $player['current_game']['rank'],
                (int)$player['current_game']['score'],
                $this->formatNumber($player['current_game']['point']),
                (int)$player['current_game']['mistake_count']
            );
            $lines[] = sprintf(
                '今日累計: 対局数:%d / 合計pt:%s / 平均pt:%s / 平均順位:%s / チョンボ:%d',
                (int)$player['today_summary']['play_count'],
                $this->formatNumber($player['today_summary']['sum_point']),
                $this->formatNumber($player['today_summary']['average_point']),
                $this->formatNumber($player['today_summary']['average_rank']),
                (int)$player['today_summary']['mistake_count']
            );
            $lines[] = sprintf(
                '順位内訳: 1着%d回 2着%d回 3着%d回 4着%d回',
                (int)($player['today_summary']['rank_count'][1] ?? 0),
                (int)($player['today_summary']['rank_count'][2] ?? 0),
                (int)($player['today_summary']['rank_count'][3] ?? 0),
                (int)($player['today_summary']['rank_count'][4] ?? 0)
            );

            foreach ($player['today_history'] as $historyRow) {
                $lines[] = sprintf(
                    '履歴: %d半荘目 %s着 素点:%d pt:%s',
                    (int)$historyRow['game'],
                    $historyRow['rank'],
                    (int)$historyRow['score'],
                    $this->formatNumber($historyRow['point'])
                );
            }
        }

        return implode("\n", $lines);
    }

    private function parseComments(string $responseText, array $expectedUserIds): ?array
    {
        $decoded = $this->decodeCommentResponse($responseText);
        if (!is_array($decoded)) {
            return null;
        }

        $expectedMap = array_fill_keys(array_map('intval', $expectedUserIds), true);
        $commentsByUser = [];

        if (isset($decoded['comments']) && is_array($decoded['comments'])) {
            $commentsByUser = $this->normalizeCommentsNode($decoded['comments'], $expectedMap);
        } elseif ($this->looksLikeUserMap($decoded)) {
            $commentsByUser = $this->normalizeCommentsNode($decoded, $expectedMap);
        } else {
            return null;
        }

        if ($commentsByUser === null) {
            return null;
        }

        if (count($commentsByUser) !== AppConstants::PLAYER_COUNT) {
            return null;
        }

        foreach ($expectedMap as $userId => $_) {
            if (!isset($commentsByUser[(string)$userId])) {
                return null;
            }
        }

        ksort($commentsByUser, SORT_NUMERIC);
        return $commentsByUser;
    }

    private function decodeCommentResponse(string $responseText): ?array
    {
        $candidates = [];
        $trimmed = trim($responseText);
        if ($trimmed !== '') {
            $candidates[] = $trimmed;
        }

        $withoutFence = preg_replace('/^```(?:json)?\s*|\s*```$/u', '', $trimmed);
        if (is_string($withoutFence) && trim($withoutFence) !== '' && trim($withoutFence) !== $trimmed) {
            $candidates[] = trim($withoutFence);
        }

        $jsonObject = $this->extractJsonObject($trimmed);
        if ($jsonObject !== null) {
            $candidates[] = $jsonObject;
        }

        foreach ($candidates as $candidate) {
            $decoded = json_decode($candidate, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    private function extractJsonObject(string $text): ?string
    {
        $start = strpos($text, '{');
        $end = strrpos($text, '}');
        if ($start === false || $end === false || $end <= $start) {
            return null;
        }

        return substr($text, $start, $end - $start + 1);
    }

    private function normalizeCommentsNode(array $commentsNode, array $expectedMap): ?array
    {
        $commentsByUser = [];

        if ($this->isAssoc($commentsNode)) {
            foreach ($commentsNode as $userId => $comment) {
                $normalizedUserId = (int)$userId;
                $normalizedComment = $this->normalizeComment((string)$comment);
                if (!isset($expectedMap[$normalizedUserId]) || $normalizedComment === '') {
                    return null;
                }

                $commentsByUser[(string)$normalizedUserId] = $normalizedComment;
            }

            return $commentsByUser;
        }

        foreach ($commentsNode as $row) {
            if (!is_array($row)) {
                return null;
            }

            $userId = null;
            $comment = null;

            if (isset($row['user_id'], $row['comment'])) {
                $userId = (int)$row['user_id'];
                $comment = (string)$row['comment'];
            } elseif (count($row) === 1) {
                $singleKey = array_key_first($row);
                if ($singleKey !== null) {
                    $userId = (int)$singleKey;
                    $comment = (string)$row[$singleKey];
                }
            }

            $normalizedComment = $this->normalizeComment((string)$comment);
            if ($userId === null || !isset($expectedMap[$userId]) || $normalizedComment === '') {
                return null;
            }

            $commentsByUser[(string)$userId] = $normalizedComment;
        }

        return $commentsByUser;
    }

    private function looksLikeUserMap(array $decoded): bool
    {
        foreach (array_keys($decoded) as $key) {
            if (!is_string($key) && !is_int($key)) {
                return false;
            }

            if (!preg_match('/^\d+$/', (string)$key)) {
                return false;
            }
        }

        return !empty($decoded);
    }

    private function isAssoc(array $array): bool
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    private function normalizeComment(string $comment): string
    {
        $comment = trim(preg_replace('/\s+/u', ' ', $comment) ?? $comment);
        if ($comment === '') {
            return '';
        }

        return mb_substr($comment, 0, 120);
    }

    private function formatNumber(float|int|string $value): string
    {
        if (is_string($value) && is_numeric($value)) {
            $value = (float)$value;
        }

        if (is_float($value)) {
            return number_format($value, 1);
        }

        return (string)$value;
    }
}
