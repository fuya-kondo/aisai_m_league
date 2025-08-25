# AISAI.M.LEAGUE - クラス化とリファクタリング

## 概要
`main_controller.php`のクラス化と、`admin_controller.php`との共通化を行いました。

## 変更内容

### 1. BaseControllerの拡張
- `controller/base_controller.php`に共通メソッドを追加
- マスターデータ取得の共通化
- CRUD操作の共通化
- JSONレスポンス処理の共通化

### 2. MainControllerの改善
- `controller/main/main_controller.php`を完全にクラス化
- BaseControllerの共通メソッドを使用
- 各ページ用のメソッドを追加
- 適切なビューファイルのインクルード

### 3. AdminControllerの改善
- `controller/admin/admin_controller.php`をリファクタリング
- BaseControllerの共通メソッドを使用
- 重複コードの削除
- レスポンス処理の統一

### 4. ルーターの追加
- `controller/router.php`を新規作成
- リクエスト処理の一元化
- エラーハンドリングの追加

### 5. URL構造の改善
- `.htaccess`ファイルを更新
- コントローラー経由でのアクセス設定
- セキュリティ向上のためのアクセス制限

### 6. CSSパスの修正
- すべてのビューファイルのCSSパスを絶対パスに修正
- `app.css`ファイルを新規作成
- 画像・動画・ファビコンパスも修正

## URL構造

### 短縮URL（推奨）

#### メインページ
- トップページ: `/` または `/top`
- 統計ページ: `/stats` または `/s`
- 履歴ページ: `/history` または `/h`
- 個人統計ページ: `/personal` または `/p`
- 分析ページ: `/analysis` または `/a`
- 設定ページ: `/setting`
- ルールページ: `/rule` または `/r`
- バッジページ: `/badge` または `/b`
- サウンドページ: `/sound`
- 追加ページ: `/add`
- 更新ページ: `/update`

#### 管理ページ
- 管理トップページ: `/admin`
- ユーザー管理: `/admin/users`
- ゲーム履歴管理: `/admin/game_history`
- マスターデータ管理: `/admin/master_data`

### 従来のURL（フォールバック）
- トップページ: `index.php?controller=main&action=top`
- 統計ページ: `index.php?controller=main&action=stats`
- 管理ページ: `index.php?controller=admin&action=top`
- など...

## セキュリティ対策

### アクセス制限
以下のディレクトリへの直接アクセスを禁止：
- `view/` - ビューファイル
- `controller/` - コントローラーファイル
- `model/` - モデルファイル
- `config/` - 設定ファイル

### ファイル構造
```
aisai-m-league.com/
├── index.php                    # エントリーポイント
├── .htaccess                    # URLリライト設定
├── config/
│   ├── .htaccess               # アクセス制限
│   ├── import_file.php         # ファイル読み込み
│   ├── db_connect.php          # データベース接続
│   └── google_gemini.php       # Google Gemini設定
├── controller/
│   ├── .htaccess               # アクセス制限
│   ├── base_controller.php     # 基底コントローラー
│   ├── router.php              # ルーター
│   ├── main/
│   │   ├── main_controller.php # メインコントローラー
│   │   └── stats_service.php   # 統計サービス
│   └── admin/
│       └── admin_controller.php # 管理コントローラー
├── model/
│   ├── .htaccess               # アクセス制限
│   └── [各種モデルファイル]
├── view/
│   ├── .htaccess               # アクセス制限
│   ├── main/                   # メインページ
│   └── admin/                  # 管理ページ
└── webroot/                    # 静的ファイル
```

## テスト方法

1. `test_urls.php`にアクセスしてURL構造をテスト
2. 各ページが正しく表示されることを確認
3. 管理機能が正常に動作することを確認

## 注意事項

- 既存のビューファイルは直接アクセスできなくなりました
- すべてのアクセスはコントローラー経由になります
- データベース接続やモデルの動作に問題がないことを確認してください

## 今後の改善点

1. エラーハンドリングの強化
2. ログ機能の追加
3. セッション管理の改善
4. キャッシュ機能の追加
5. APIエンドポイントの整理
