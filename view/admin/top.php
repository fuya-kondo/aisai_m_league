<?php
    require_once __DIR__ . '/../../config/import_file.php';
    $baseUrl = getBaseUrl();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?></title>
    <link rel="stylesheet" href="../../resources/css/master.css">
    <link rel="stylesheet" href="../../resources/css/header.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .admin-header {
            text-align: center;
            padding: 20px;
        }
        .admin-title {
            font-size: 2rem;
            color: #009944;
            margin-bottom: 10px;
        }
        .admin-subtitle {
            color: #666;
            font-size: 1.1rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #009944;
            margin-bottom: 10px;
        }
        .stat-label {
            color: #666;
            font-size: 1.1rem;
        }
        .nav-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .nav-card {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
        }
        .nav-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }
        .nav-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        .nav-title {
            font-size: 1.3rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .nav-description {
            color: #666;
            line-height: 1.5;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #6c757d;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            transition: background-color 0.2s ease;
        }
        .back-link:hover {
            background: #5a6268;
        }
        @media (max-width: 768px) {
            .admin-container {
                padding: 10px;
            }
            .admin-title {
                font-size: 1.5rem;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .nav-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1 class="admin-title">管理画面</h1>
            <p class="admin-subtitle">AISAI.M.LEAGUE<br>データベース管理システム</p>
        </div>

        <div class="nav-grid">
            <div class="nav-card" onclick="location.href='?controller=admin&action=users'">
                <div class="nav-icon">👥</div>
                <div class="nav-title">ユーザー管理</div>
                <div class="nav-description">
                    ユーザー情報、バッジ、ティア、タイトルの管理を行います。
                    新規ユーザーの追加、既存ユーザーの編集・削除が可能です。
                </div>
            </div>

            <div class="nav-card" onclick="location.href='?controller=admin&action=game_history'">
                <div class="nav-icon">🎮</div>
                <div class="nav-title">ゲーム履歴管理</div>
                <div class="nav-description">
                    ゲーム履歴の確認、編集、削除を行います。
                    スコアや結果の修正、不正なデータの削除が可能です。
                </div>
            </div>

            <div class="nav-card" onclick="location.href='?controller=admin&action=master_data'">
                <div class="nav-icon">⚙️</div>
                <div class="nav-title">マスターデータ管理</div>
                <div class="nav-description">
                    バッジ、ティア、タイトル、ルール、設定などの
                    マスターデータの管理を行います。
                </div>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="<?= $baseUrl ?>/top" class="back-link">← メインサイトに戻る</a>
        </div>
    </div>

    <script>
        // 管理画面の基本機能
        document.addEventListener('DOMContentLoaded', function() {
            // ナビゲーションカードのクリックイベント
            document.querySelectorAll('.nav-card').forEach(card => {
                card.addEventListener('click', function() {
                    // クリック時の視覚的フィードバック
                    this.style.transform = 'scale(0.98)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });
        });
    </script>
</body>
</html>
