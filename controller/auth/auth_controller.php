<?php

/**
 * 認証APIコントローラー。
 */
class AuthController extends BaseController
{
    /**
     * /auth/login
     * roleクレームを含むJWTを発行する。
     */
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            $this->errorResponse('Method Not Allowed');
            return;
        }

        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $throttle = AuthSecurity::checkLoginThrottle($ipAddress);
        if (!$throttle['allowed']) {
            http_response_code(429);
            header('Retry-After: ' . $throttle['retry_after']);
            $this->errorResponse('ログイン試行回数が上限を超えました。しばらく待ってから再試行してください。');
            return;
        }

        $request = $this->readJsonBody();
        $username = trim((string)($request['username'] ?? ''));
        $password = (string)($request['password'] ?? '');

        $auth = $this->authenticate($username, $password);
        if ($auth === null) {
            AuthSecurity::recordLoginFailure($ipAddress);
            http_response_code(401);
            $this->errorResponse('認証に失敗しました');
            return;
        }

        AuthSecurity::clearLoginFailures($ipAddress);

        $now = time();
        $ttl = (int)(getenv('JWT_TTL_SECONDS') ?: 3600);
        $claims = [
            'sub' => $auth['username'],
            'role' => $auth['role'],
            'iat' => $now,
            'exp' => $now + $ttl,
        ];

        $secret = getenv('JWT_SECRET') ?: 'local-dev-secret';
        $token = JwtHelper::encode($claims, $secret);

        $this->successResponse([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $ttl,
            'role' => $auth['role'],
        ]);
    }

    private function readJsonBody(): array
    {
        $raw = file_get_contents('php://input');
        $json = json_decode((string)$raw, true);
        if (is_array($json)) {
            return $json;
        }

        return $_POST;
    }

    /**
     * 環境変数ベースの簡易認証。
     */
    private function authenticate(string $username, string $password): ?array
    {
        $adminUser = getenv('ADMIN_LOGIN_ID') ?: 'admin';
        $adminPass = getenv('ADMIN_LOGIN_PASSWORD') ?: '';
        $generalUser = getenv('GENERAL_LOGIN_ID') ?: 'user';
        $generalPass = getenv('GENERAL_LOGIN_PASSWORD') ?: '';

        if ($username === $adminUser && $adminPass !== '' && hash_equals($adminPass, $password)) {
            return ['username' => $username, 'role' => 'admin'];
        }

        if ($username === $generalUser && $generalPass !== '' && hash_equals($generalPass, $password)) {
            return ['username' => $username, 'role' => 'general'];
        }

        return null;
    }
}
