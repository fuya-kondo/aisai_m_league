<?php
// Include necessary files
require_once __DIR__ . '/../config/import_file.php';
// Include header
include '../webroot/common/header.php';
// Include GeminiAPI
use GeminiAPI\Resources\Parts\TextPart;


// 分析メッセージ
$analysisMsg = <<<EOT
html上で出力するので見やすいようにしてください。(
麻雀の成績を元に指定した選手の分析をしてほしいです。
Mリーグルール(https://m-league.jp/about/)を採用しています。
分析の指標として以下内容を参考にしてください。
平均着順：平均は2.50です。0に近いほど良い。
トップ率：平均は25%です。100%に近いほど良い。
ラス回避率：平均は75%です。100%に近いほど良い。
合計点：素点と順位点を足したものです。一番の指標となります。このポイントが高い方がランキングが上になります。高ければ高いほど良い。
合計点と素点の開きが大きいほど順位取りがうまいと言えます。
連対率：1,2位を取った割合のことです。平均は50%です。100%に近いほど良い。
平均点：25000点から始まって、終了時の点数のことです。高ければ高いほど良い。
EOT;

// Get parameter
$selectUser = $_GET['userId'] ?? null;
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="../webroot/css/master.css">
    <link rel="stylesheet" href="../webroot/css/header.css">
    <link rel="stylesheet" href="../webroot/css/button.css">
    <link rel="stylesheet" href="../webroot/css/table.css">
    <title>麻雀成績分析</title>
</head>
<body>
    <main>
        <div class="container">
            <?php if (!isset($selectUser)): ?>
                <div class="page-title">ユーザーを分析</div>
                <div class="select-button-container">
                    <form action="analysis" method="get">
                        <form action="history" method="get">
                            <?php foreach($userList as $userId => $userData): ?>
                                <button class="select-button" type="submit" name="userId" value="<?=$userId?>"><?=$userData[0]['last_name'].$userData[0]['first_name']?></button>
                            <?php endforeach; ?>
                        </form>
                    </form>
                </div>
                <div class="circle-parent" style="display:none">
                    <div class="circle-spin-8"></div>
                </div>
            <?php else: ?>
            <?php
                $userName = $userList[$selectUser][0]['last_name'].$userList[$selectUser][0]['first_name'];

                // AIへの指示文を作成
                $set_msg = $analysisMsg;
                $set_msg .= "麻雀の成績から{$userName}選手の成績まとめ,特徴,改善点を300文字前後で出力してください。\n他にも気づいたことがあれば追加してもよいです。";

                $score_msg = "以下、{$analysisData[$selectUser]['name']}の成績です。\n";
                $score_msg .= "成績まとめ。データは左から選手ID、選手名、対局数、合計点、素点、平均着順、1着、2着、3着、4着、トップ率、連対率、ラス回避率、終了時平均点数の順で並んでいます。\n";
                $score_msg .= implode(', ', $analysisData[$selectUser]) . "\n";

                $msg = $set_msg . "\n" . $score_msg;

                // 成績表示
                $statsTable = "<div class='table-container'><table class='score-table'><tr>";

                foreach ($analysisData[$selectUser] as $key => $value) {
                    if (!isset($statsColumnConfig[$key])) continue;
                    $statsTable .= "<th>" . htmlspecialchars($statsColumnConfig[$key]) . "</th>";
                }
                $statsTable .= "</tr><tr>";

                foreach ($analysisData[$selectUser] as $key => $value) {
                    if (!isset($statsColumnConfig[$key])) continue;
                    $statsTable .= "<td>" . htmlspecialchars($value) . "</td>";
                }
                $statsTable .= "</tr></table></div><br>";

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
                        echo $result['candidates'][0]['content']['parts'][0]['text'];
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
<script>
    document.querySelectorAll('.select-button').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelector('.select-button-container').style.display = 'none';
            document.querySelector('.circle-parent').style.display = 'flex';
        });
    });
</script>
