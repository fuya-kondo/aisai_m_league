<?php
// Include necessary files
require_once __DIR__ . '/../config/import_file.php';
// Include header
include '../webroot/common/header.php';
// Include GeminiAPI
use GeminiAPI\Resources\Parts\TextPart;


// 分析メッセージ
$analysisMsg = <<<EOT
麻雀の成績データを元に、指定された選手の分析をお願いします。
出力はHTML形式で見やすくしてください。

Mリーグルールを適用した成績データです。分析の際は以下の指標を参考にしてください。

-   **平均順位**: 平均は **2.50** です。数値が低いほど良い成績です。
-   **トップ率**: 平均は **25%** です。数値が高いほど良い成績です。
-   **ラス回避率**: 平均は **75%** です。数値が高いほど良い成績です。
-   **合計点 (素点 + 順位点)**: これが最も重要な指標です。高いほど総合的な成績が良いことを示します。素点との開きが大きい場合、順位取りが上手い傾向があります。
-   **連対率 (1位・2位率)**: 平均は **50%** です。数値が高いほど良い成績です。
-   **平均点 (対局終了時)**: 25000点から開始し、終了時の平均点数です。数値が高いほど良い成績です。
-   **チョンボ数**: 対局中に罰則に該当する行為をした回数です。1回で合計ポイントから20ポイント引かれてしまうため影響が大きいです。0回が理想的です。

提供される成績データは、これらの指標を基にしたものです。
EOT;

// Get parameter
$selectUser = $_GET['userId'] ?? null;

// Set title
$title = 'AI成績分析';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <link rel="apple-touch-icon" href="../favicon.png">
    <link rel="icon" href="../favicon.ico" sizes="64x64" type="image/x-icon">
    <link rel="stylesheet" href="../webroot/css/master.css">
    <link rel="stylesheet" href="../webroot/css/header.css">
    <link rel="stylesheet" href="../webroot/css/button.css">
    <link rel="stylesheet" href="../webroot/css/table.css">
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
                $userName = $userList[$selectUser]['last_name'].$userList[$selectUser]['first_name'];

                // AIへの指示文を作成
                $set_msg = $analysisMsg;
                $set_msg .= "麻雀の成績データに基づいて、{$userName}選手の成績まとめ、特徴、改善点を300文字前後で出力してください。各家別成績はまだデータ数が少ないのであまり参考にしないでください。他にも気づいたことがあれば追加しても構いません。";

                $score_msg = "以下に{$userName}選手の麻雀成績の詳細データを示します。\n\n";

                // 主要な個人成績
                $score_msg .= "## 個人主要成績\n";
                foreach ($analysisDataList[$selectUser] as $key => $value) {
                    // statsNameConfig に定義されている主要な単一値のみを抽出
                    // rank_count, rank_probability, direction系の配列はここでは除外
                    if (isset($statsNameConfig[$key]) && !is_array($value)) {
                        $score_msg .= htmlspecialchars($statsNameConfig[$key]) . ": " . htmlspecialchars($value) . "\n";
                    }
                }
                $score_msg .= "\n";

                // 順位ごとの回数と確率
                if (isset($analysisDataList[$selectUser]['rank_count']) && is_array($analysisDataList[$selectUser]['rank_count'])) {
                    $score_msg .= "## 順位別回数と確率\n";
                    $rankCounts = [];
                    $rankProbs = [];
                    foreach ($analysisDataList[$selectUser]['rank_count'] as $rank => $count) {
                        $rankCounts[] = "{$rank}着: {$count}回";
                        // rank_probabilityも同時に取得できるなら
                        if (isset($analysisDataList[$selectUser]['rank_probability'][$rank])) {
                            $rankProbs[] = "{$rank}着率: " . htmlspecialchars($analysisDataList[$selectUser]['rank_probability'][$rank]);
                        }
                    }
                    $score_msg .= implode(", ", $rankCounts) . "\n";
                    if (!empty($rankProbs)) {
                        $score_msg .= implode(", ", $rankProbs) . "\n";
                    }
                    $score_msg .= "\n";
                }

                // 各家（東・南・西・北）ごとの成績
                if (isset($analysisDataList[$selectUser]['play_count_direction']) && is_array($analysisDataList[$selectUser]['play_count_direction'])) {
                    $score_msg .= "## 各家別成績\n";
                    foreach ($mDirectionList as $directionId => $directionName) {
                        if (isset($analysisDataList[$selectUser]['play_count_direction'][$directionId])) {
                            $score_msg .= "### " . htmlspecialchars($directionName) . "\n";
                            $score_msg .= "対局数: " . htmlspecialchars($analysisDataList[$selectUser]['play_count_direction'][$directionId]) . "\n";

                            // 各家ごとの順位回数と確率
                            if (isset($analysisDataList[$selectUser]['rank_count_direction'][$directionId]) && is_array($analysisDataList[$selectUser]['rank_count_direction'][$directionId])) {
                                $rankDirectionCounts = [];
                                $rankDirectionProbs = [];
                                foreach ($analysisDataList[$selectUser]['rank_count_direction'][$directionId] as $rank => $count) {
                                    $rankDirectionCounts[] = "{$rank}着: {$count}回";
                                    if (isset($analysisDataList[$selectUser]['rank_probability_direction'][$directionId][$rank])) {
                                        $rankDirectionProbs[] = "{$rank}着率: " . htmlspecialchars($analysisDataList[$selectUser]['rank_probability_direction'][$directionId][$rank]);
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
                                    $score_msg .= htmlspecialchars($statName) . ": " . htmlspecialchars($analysisDataList[$selectUser][$statKey][$directionId]) . "\n";
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
    .container {
        overflow-x: auto;
    }
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
