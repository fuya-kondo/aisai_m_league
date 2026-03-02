# ローカル開発手順

## Docker でのローカル起動
### 前提
* Docker / Docker Compose が使えること

### 起動
```bash
docker compose up -d --build
```

### 省略コマンド（WSL向け）
* リポジトリ直下に `dc` を追加済みです。`docker.exe compose` のラッパーです。
```bash
./dc up -d --build
./dc ps
./dc logs -f web
./dc down
```

### アクセス先
* アプリ: `http://localhost:8080`
* 4人登録画面: `http://localhost:8080/add4`
* phpMyAdmin: `http://localhost:8081`
  * ユーザー: `root`
  * パスワード: `rootpass`

### DB初期データについて
* `fuyakondo_aisaimleague.sql` は `db` コンテナ初回起動時に自動で投入されます。
* `config/db_connect.php` は存在しない場合、`web` コンテナ起動時に自動生成されます。

### 停止
```bash
docker compose down
```

### DBを初期化し直す
```bash
docker compose down -v
docker compose up -d --build
```
