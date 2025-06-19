<?php

date_default_timezone_set('Asia/Tokyo');

// ----------------------------------------------------------------------
// データベース設定
// ----------------------------------------------------------------------
/**
 * サーバのホスト名に基づいてデータベース設定を選択します。
 * 本番環境では、この設定を環境変数や設定ファイルから読み込むことを強く推奨します。
 *
 * @return array 選択されたデータベース設定
 * @throws Exception ホスト名に対応する設定が見つからない場合
 */
function getDatabaseConfig(): array
{
    $hostname = $_SERVER['HTTP_HOST'];

    $configs = [
        'localhost' => [
            'dbUser'   => 'root',
            'dbPass'   => '',
            'dbServer' => '127.0.0.1',
            'dbName'   => 'fuyakondo_aisaimleague',
        ],
        'default' => [
            'dbUser'   => 'fuyakondo_aisai',
            'dbPass'   => 'aisaimleague',
            'dbServer' => 'localhost',
            'dbName'   => 'fuyakondo_aisaimleague',
        ],
    ];

    if (isset($configs[$hostname])) {
        return $configs[$hostname];
    } elseif (array_key_exists('default', $configs)) {
        return $configs['default'];
    }

    throw new Exception('データベース設定が見つかりません。ホスト名: ' . $hostname);
}

// ----------------------------------------------------------------------
// データベース接続管理クラス
// ----------------------------------------------------------------------
class Database
{
    private PDO $pdo;
    private static ?Database $instance = null; // null許容型として宣言

    /**
     * Database constructor.
     * データベース設定を受け取り、PDO接続を確立します。
     *
     * @param array $config データベース接続設定 (dbUser, dbPass, dbServer, dbName を含む)
     * @throws Exception データベース接続に失敗した場合
     */
    private function __construct(array $config)
    {
        $dsn = "mysql:host={$config['dbServer']};dbname={$config['dbName']};charset=utf8mb4"; // 文字コードを追加

        try {
            $this->pdo = new PDO($dsn, $config['dbUser'], $config['dbPass']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // デフォルトのフェッチモードを設定
        } catch (PDOException $e) {
            throw new Exception('データベース接続に失敗しました。管理者にお問い合わせください。');
        }
    }

    /**
     * Databaseクラスのシングルトンインスタンスを取得します。
     * 最初の呼び出し時にのみ、データベース設定に基づいてインスタンスを生成します。
     *
     * @param array $config (初回呼び出し時のみ必須) データベース接続設定
     * @return Database シングルトンインスタンス
     * @throws Exception データベース設定が渡されない場合、または接続失敗時
     */
    public static function getInstance(array $config = []): Database
    {
        if (self::$instance === null) {
            if (empty($config)) {
                throw new Exception('データベース接続設定が提供されていません。');
            }
            self::$instance = new Database($config);
        }
        return self::$instance;
    }

    /**
     * PDO接続オブジェクトを取得します。
     *
     * @return PDO データベース接続オブジェクト
     */
    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    /**
     * クローンを禁止します。（シングルトンパターン用）
     */
    private function __clone() {}

    /**
     * シリアル化を禁止します。（シングルトンパターン用）
     */
    public function __sleep()
    {
        throw new Exception('シリアライズは許可されていません。');
    }

    /**
     * デシリアル化を禁止します。（シングルトンパターン用）
     */
    public function __wakeup()
    {
        throw new Exception('デシリアライズは許可されていません。');
    }
}