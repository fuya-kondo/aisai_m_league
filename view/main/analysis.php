<?php

// Include header
include __DIR__ . '/../header.php';

// Include GeminiAPI
use GeminiAPI\Resources\Parts\TextPart;
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <link rel="apple-touch-icon" href="<?= $baseUrl ?>/favicon.png">
    <link rel="icon" href="<?= $baseUrl ?>/favicon.ico" sizes="64x64" type="image/x-icon">
    <link rel="stylesheet" href="<?= $baseUrl ?>/resources/css/master.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>/resources/css/header.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>/resources/css/app.css">
    <title><?= $title ?></title>
</head>
<body>
<main>
    <div class="container">
        <?php if (!isset($selectUser)): ?>
            <div class="page-title"><?= $title ?></div>
            <div class="select-button-container">
                <form action="analysis" method="get">
                    <form action="history" method="get">
                        <?php foreach($userList as $userId => $userData): ?>
                            <button class="select-button" type="submit" name="userId" value="<?=$userId?>"><?=$userData['last_name'].$userData['first_name']?></button>
                        <?php endforeach; ?>
                    </form>
                </form>
            </div>
            <div class="circle-parent" style="display:none">
                <div class="circle-spin-8"></div>
            </div>
        <?php else: ?>
            <?php
                // 各家（東・南・西・北）ごとの成績
                if (isset($analysisDataList[$selectUser]['play_count_direction']) && is_array($analysisDataList[$selectUser]['play_count_direction'])) {
                    $score_msg .= "## 各家別成績\n";
                    foreach ($mDirectionList as $directionId => $directionData) {
                        if (isset($analysisDataList[$selectUser]['play_count_direction'][$directionId])) {
                            $score_msg .= "### " . htmlspecialchars($directionData['name']) . "\n";
                            $playCount = $analysisDataList[$selectUser]['play_count_direction'][$directionId];
                            if (is_array($playCount)) {
                                $playCount = implode(', ', $playCount);
                            }
                            $score_msg .= "対局数: " . htmlspecialchars($playCount) . "\n";

                            // 各家ごとの順位回数と確率
                            if (isset($analysisDataList[$selectUser]['rank_count_direction'][$directionId]) && is_array($analysisDataList[$selectUser]['rank_count_direction'][$directionId])) {
                                $rankDirectionCounts = [];
                                $rankDirectionProbs = [];
                                foreach ($analysisDataList[$selectUser]['rank_count_direction'][$directionId] as $rank => $count) {
                                    $rankDirectionCounts[] = "{$rank}着: {$count}回";
                                    if (isset($analysisDataList[$selectUser]['rank_probability_direction'][$directionId][$rank])) {
                                        $probValue = $analysisDataList[$selectUser]['rank_probability_direction'][$directionId][$rank];
                                        if (is_array($probValue)) {
                                            $probValue = implode(', ', $probValue);
                                        }
                                        $rankDirectionProbs[] = "{$rank}着率: " . htmlspecialchars($probValue);
                                    }
                                }
                                $score_msg .= "順位別回数: " . implode(", ", $rankDirectionCounts) . "\n";
                                if (!empty($rankDirectionProbs)) {
                                    $score_msg .= "順位別確率: " . implode(", ", $rankDirectionProbs) . "\n";
                                }
                            }
                            // 各家ごとのその他の指標 (平均順位、平均点数など)
                            $directionStats = [
                                'average_rank_direction' => '平均順位',
                                'average_score_direction' => '平均点数',
                                'average_point_direction' => '平均収支(pt)',
                                // 必要に応じて他のキーを追加
                            ];
                            foreach ($directionStats as $statKey => $statName) {
                                if (isset($analysisDataList[$selectUser][$statKey][$directionId])) {
                                    $value = $analysisDataList[$selectUser][$statKey][$directionId];
                                    if (is_array($value)) {
                                        $value = implode(', ', $value);
                                    }
                                    $score_msg .= htmlspecialchars($statName) . ": " . htmlspecialchars($value) . "\n";
                                }
                            }
                            $score_msg .= "\n"; // 各家の区切り
                        }
                    }
                }

                $msg = $set_msg . "\n" . $score_msg;

                try {
                    $data = [
                        'contents' => [[
                            'parts' => [
                                ['text' => $msg]
                            ]
                        ]]
                    ];
                    $options = [
                        'http' => [
                            'method'  => 'POST',
                            'header'  => 'Content-Type: application/json',
                            'content' => json_encode($data)
                        ]
                    ];
                    $context  = stream_context_create($options);
                    $response = file_get_contents($url, false, $context);

                    if ($response === FALSE) die('Error occurred while sending request');
                    $result = json_decode($response, true);
                    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                        $search = ['```html', '```'];
                        $clean_text = str_replace($search, '', $result['candidates'][0]['content']['parts'][0]['text']);
                        echo $clean_text;
                    } else {
                        echo "No response text found.";
                    }
                } catch (\Exception $e) {
                    echo "<div class='error'>エラーが発生しました: " . htmlspecialchars($e->getMessage()) . "</div>";
                }
            ?>
        <?php endif; ?>
    </div>
</main>
</body>
</html>

<script>
    document.querySelectorAll('.select-button').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelector('.select-button-container').style.display = 'none';
            document.querySelector('.circle-parent').style.display = 'flex';
        });
    });
</script>

<style>
    /* ページ固有の微調整がある場合のみここに追加 */
    .table-responsive {
        overflow-x: auto;
        margin-bottom: 20px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th,td {
        padding: 8px;
        border: 1px solid #ddd;
        white-space: nowrap;
    }
    .textarea {
        text-align: center;
        margin-bottom: 20px;
    }
    textarea {
        width: 100%;
        max-width: 500px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    @media screen and (max-width: 768px) {
        textarea {
            padding: 0;
        }
    }
    .circle-parent {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .circle-spin-8 {
        --size: 24px;
        --color: currentColor;
        --animation-timing-function: linear;
        --animation-duration: 2s;
        width: var(--size);
        height: var(--size);
        mask-image: radial-gradient(circle at 50% 50%, transparent calc(var(--size) / 3), black calc(var(--size) / 3));
        background-image: conic-gradient(transparent, transparent 135deg, currentColor);
        border-radius: 50%;
        animation: var(--animation-timing-function) var(--animation-duration) infinite circle-spin-8-animation;
    }
    @keyframes circle-spin-8-animation {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
</style>
