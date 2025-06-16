<?php
date_default_timezone_set('Asia/Tokyo');

/**
 * サーバ選択
 */
$hostname = $_SERVER['HTTP_HOST'];
if ( $hostname === 'localhost' ) {
    $dbServerNumber = 1; // local
} else {
    $dbServerNumber = 2; // XServer
}

/**
 * データベース接続情報
 */
$dbUser   = ($dbServerNumber == 1) ? 'root'                   : 'fuyakondo_aisai';
$dbPass   = ($dbServerNumber == 1) ? ''                       : 'aisaimleague';
$dbServer = ($dbServerNumber == 1) ? '127.0.0.1'              : 'localhost';
$dbName   = ($dbServerNumber == 1) ? 'fuyakondo_aisaimleague' : 'fuyakondo_aisaimleague';

$dsn = "mysql:host=$dbServer;dbname=$dbName;";

// ----------------------------------------------------------------------
// データベース接続管理クラス
// ----------------------------------------------------------------------
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct(string $dsn, string $user, string $password) {
        try {
            $this->pdo = new PDO($dsn, $user, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // 例外を投げるように設定
        } catch (PDOException $e) {
            throw new Exception('データベース接続失敗: ' . $e->getMessage());
        }
    }

    public static function getInstance(string $dsn, string $user, string $password): Database {
        if (self::$instance === null) {
            self::$instance = new Database($dsn, $user, $password);
        }
        return self::$instance;
    }

    public function getConnection(): PDO {
        return $this->pdo;
    }
}
?>