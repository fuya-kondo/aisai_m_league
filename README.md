# 麻雀成績管理サイト

* ※身内間ですが、サービス稼働中

## 開発環境
* バックエンド：PHP
* フロントエンド：JavaScript, HTML, CSS
* データベース：MySQL
* インフラ/環境：XAMPP（ローカル環境）、Xserver（本番デプロイ）

---

## 機能一覧
<img width="329" height="508" alt="image" src="https://github.com/user-attachments/assets/b39f1091-ae7f-4dec-b10d-3cb5be04f439" />

## 全体成績
<img width="327" height="643" alt="image" src="https://github.com/user-attachments/assets/8ae98331-b8fd-42c8-9ca4-3ba68ba175b6" />

## 個人成績
<img width="323" height="565" alt="image" src="https://github.com/user-attachments/assets/0f0efc29-18e1-4dbb-9cab-464e62f58300" /><img width="325" height="583" alt="image" src="https://github.com/user-attachments/assets/4c51bf50-02a7-44df-b725-9d2160be66ab" /><img width="326" height="646" alt="image" src="https://github.com/user-attachments/assets/f31f357a-dc1c-4823-90e4-38f30f92b6f9" /><img width="323" height="165" alt="image" src="https://github.com/user-attachments/assets/8b3ba09e-97d2-4c64-901b-45c12b95b9ad" />

### 称号変更
<img width="319" height="620" alt="image" src="https://github.com/user-attachments/assets/c0799558-9d14-4e21-838d-8a829c5e7513" />

## 成績登録
<img width="321" height="522" alt="image" src="https://github.com/user-attachments/assets/e3feb77d-2de8-4515-8b95-51689b170c09" />

## 成績履歴
<img width="315" height="636" alt="image" src="https://github.com/user-attachments/assets/9adaa3ea-2010-405e-bbe9-f44c8d51c362" />

### 成績個人履歴
<img width="320" height="548" alt="image" src="https://github.com/user-attachments/assets/a1c7766e-41b9-4607-818e-2ac664cb0490" />

## Admin
<img width="319" height="647" alt="image" src="https://github.com/user-attachments/assets/7b4c07b8-7f06-47f2-bf14-83a35493fee0" />
<img width="325" height="495" alt="image" src="https://github.com/user-attachments/assets/01e34755-b644-4a85-b757-d0c89d217b6e" />


# DB 設計書

## 全体像

* **命名規則**: マスタは `m_*`、ユーザー・利用系は `u_*`
* **主な機能**: 卓（テーブル）編成、対局履歴、階級（Tier）、称号（Badge）、ルール設定。
* **リレーション（論理）**

  * ルール(`m_rule`) ← グループ(`m_group`) ← 卓(`u_table`) ← 対局履歴(`u_game_history`)
  * ユーザー(`u_user`) ← 対局履歴(`u_game_history`)
  * 自家(`m_direction`) ← 対局履歴(`u_game_history`)
  * ユーザー(`u_user`) ← 階級履歴(`u_tier_history`) → 階級(`m_tier`)
  * ユーザー(`u_user`) → 称号(`m_badge`)

---

## ER 図（Mermaid）

```mermaid
erDiagram
  m_rule ||--o{ m_group : rule
  m_group ||--o{ u_table : group
  u_table ||--o{ u_game_history : table
  u_user ||--o{ u_game_history : player
  m_direction ||--o{ u_game_history : seat
  u_user ||--o{ u_tier_history : history
  m_tier ||--o{ u_tier_history : to_tier
  m_badge ||--o{ u_user : badge

  m_rule {
    int m_rule_id PK
    string name
    int start_score
    int end_score
    int point_1
    int point_2
    int point_3
    int point_4
  }
  m_group {
    int m_group_id PK
    string name
    int m_rule_id FK
  }
  u_table {
    int u_table_id PK
    int m_group_id FK
    int u_user_id_1 FK
    int u_user_id_2 FK
    int u_user_id_3 FK
    int u_user_id_4 FK
  }
  u_game_history {
    int u_game_history_id PK
    int u_table_id FK
    int u_user_id FK
    int game
    int m_direction_id FK
    string rank
    int score
    decimal point
    int mistake_count
    datetime play_date
    int del_flag
    datetime reg_date
    timestamp update_date
  }
  u_user {
    int u_user_id PK
    string last_name
    string first_name
    int m_tier_id FK
    int m_badge_id FK
    datetime reg_date
    timestamp update_date
  }
  u_tier_history {
    int u_user_tier_history_id PK
    int u_user_id FK
    int m_tier_id FK
    int change_date
  }
  m_tier {
    int m_tier_id PK
    string name
    string color
  }
  m_badge {
    int m_badge_id PK
    string name
    string image
    string flame
    string background
  }
  m_direction {
    int m_direction_id PK
    string name
  }
```

---

## テーブル定義（詳細）

### 1) m_rule — ルール

| 列           | 型                    | Null | 既定値       | 説明                 |
| ----------- | -------------------- | ---- | --------- | ------------------ |
| m_rule_id   | TINYINT(3) UNSIGNED  | NO   | -         | PK                 |
| name        | VARCHAR(64)          | NO   | 'UNKNOWN' | ルール名（例: Mリーグ/最高位戦） |
| start_score | SMALLINT(5) UNSIGNED | NO   | 0         | 配原点（配分の起点点数）       |
| end_score   | SMALLINT(5) UNSIGNED | NO   | 0         | 返し点（精算の基準点）        |
| point_1..4  | TINYINT(3)           | NO   | 0         | 順位ウマ（1〜4位）         |

**ポイント計算**: `((score - end_score) / 1000) + UMA(rank)`

### 2) m_group — グループ（卓の種別）

| 列          | 型                   | Null | 既定値       | 説明             |
| ---------- | ------------------- | ---- | --------- | -------------- |
| m_group_id | TINYINT(3) UNSIGNED | NO   | -         | PK             |
| name       | VARCHAR(64)         | NO   | 'UNKNOWN' | グループ名（A卓・B卓など） |
| m_rule_id  | TINYINT(3) UNSIGNED | NO   | 0         | 適用ルール FK       |

### 3) u_table — 卓（対局メンバー）

| 列              | 型                   | Null | 既定値 | 説明                    |
| -------------- | ------------------- | ---- | --- | --------------------- |
| u_table_id     | TINYINT(3) UNSIGNED | NO   | -   | PK                    |
| m_group_id     | TINYINT(3) UNSIGNED | NO   | 0   | グループ FK（→ m_group）    |
| u_user_id_1..4 | TINYINT(3) UNSIGNED | NO   | 0   | その卓の参加者 1〜4（→ u_user） |

### 4) u_game_history — 対局履歴

| 列                 | 型                    | Null | 既定値               | 説明                                  |
| ----------------- | -------------------- | ---- | ----------------- | ----------------------------------- |
| u_game_history_id | SMALLINT(5) UNSIGNED | NO   | -                 | PK                                  |
| u_table_id        | TINYINT(3) UNSIGNED  | NO   | 0                 | 卓 FK（→ u_table）                     |
| u_user_id         | TINYINT(3) UNSIGNED  | NO   | 0                 | ユーザー FK（→ u_user）                   |
| game              | TINYINT(3) UNSIGNED  | NO   | 0                 | 同一卓での試合番号（1,2,3…）                   |
| m_direction_id    | INT(3) UNSIGNED      | NO   | 0                 | 自家             |
| rank              | VARCHAR(32)          | NO   | 'UNKNOWN'         | 順位 |
| score             | INT(6)               | NO   | 0                 | 素点（終局時スコア）                          |
| point             | DECIMAL(5,1)         | NO   | 0.0               | 精算ポイント（返し+ウマ適用後）                    |
| mistake_count     | TINYINT(3) UNSIGNED  | NO   | 0                 | チョンボ回数                              |
| play_date         | DATETIME             | NO   | '1000-01-01'      | 対局日時                                |
| del_flag          | TINYINT(3) UNSIGNED  | NO   | 0                 | ソフトデリート用フラグ                         |
| reg_date          | DATETIME             | NO   | '1000-01-01'      | 登録日時                                |
| update_date       | TIMESTAMP            | NO   | CURRENT_TIMESTAMP | 更新日時（ON UPDATE）                     |

### 5) u_user — ユーザー

| 列           | 型                   | Null | 既定値               | 説明                           |
| ----------- | ------------------- | ---- | ----------------- | ---------------------------- |
| u_user_id   | TINYINT(3) UNSIGNED | NO   | -                 | PK                           |
| last_name   | VARCHAR(64)         | NO   | 'UNKNOWN'         | 姓                            |
| first_name  | VARCHAR(64)         | NO   | 'UNKNOWN'         | 名 |
| m_tier_id   | TINYINT(3) UNSIGNED | NO   | 0                 | 現在の階級（→ m_tier）              |
| m_badge_id  | TINYINT(3) UNSIGNED | NO   | 0                 | 現在の称号（→ m_badge）             |
| reg_date    | DATETIME            | NO   | CURRENT_TIMESTAMP | 登録日時                         |
| update_date | TIMESTAMP           | NO   | CURRENT_TIMESTAMP | 更新日時（ON UPDATE）              |

### 6) u_tier_history — ユーザー階級の変更履歴

| 列                      | 型                   | Null | 既定値 | 説明                |
| ---------------------- | ------------------- | ---- | --- | ----------------- |
| u_user_tier_history_id | INT(10) UNSIGNED    | NO   | -   | PK                |
| u_user_id              | TINYINT(3) UNSIGNED | NO   | -   | ユーザー（→ u_user）    |
| m_tier_id              | TINYINT(3) UNSIGNED | NO   | -   | 適用された階級（→ m_tier） |
| change_date            | YEAR(4)             | NO   | -   | 変更年               |

### 7) m_tier — 階級

| 列         | 型                   | Null | 既定値 | 説明             |
| --------- | ------------------- | ---- | --- | -------------- |
| m_tier_id | TINYINT(3) UNSIGNED | NO   | -   | PK             |
| name      | VARCHAR(32)         | NO   | -   | 階級名（S/A/B/C/D） |
| color     | VARCHAR(32)         | NO   | -   | 表示色コード（HEX など） |

### 8) m_badge — 称号

| 列          | 型            | Null | 既定値 | 説明       |
| ---------- | ------------ | ---- | --- | -------- |
| m_badge_id | TINYINT(3)   | NO   | -   | PK       |
| name       | VARCHAR(64)  | NO   | -   | 称号名      |
| image      | VARCHAR(255) | NO   | -   | 画像ファイル名  |
| flame      | VARCHAR(255) | NO   | -   | 枠（未使用可）  |
| background | VARCHAR(255) | NO   | -   | 背景（未使用可） |

### 9) m_direction — 自家（東南西北）

| 列              | 型                   | Null | 既定値 | 説明                        |
| -------------- | ------------------- | ---- | --- | ------------------------- |
| m_direction_id | TINYINT(3) UNSIGNED | NO   | -   | PK（1:東 / 2:南 / 3:西 / 4:北） |
| name           | VARCHAR(64)         | NO   | -   | 表示名                       |

### 10) m_setting — システム設定

| 列            | 型                    | Null | 既定値 | 説明            |
| ------------ | -------------------- | ---- | --- | ------------- |
| m_setting_id | TINYINT(3) UNSIGNED  | NO   | -   | PK            |
| name         | VARCHAR(255)         | NO   | -   | 設定名（例: 点数非表示） |
| value        | SMALLINT(5) UNSIGNED | NO   | 0   | 値             |

