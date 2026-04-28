<?php
/**
 * JWT Authentication
 */

class Autenticacion {
    private static $secret = 'ucm-totem-super-secret-key-2026';
    private static $expiry = 28800; // 8 hours in seconds

    public static function createToken($user) {
        $payload = [
            'iat' => time(),
            'exp' => time() + self::$expiry,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ]
        ];

        $header = base64_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payloadEncoded = base64_encode(json_encode($payload));
        $signature = hash_hmac('sha256', "$header.$payloadEncoded", self::$secret);

        return "$header.$payloadEncoded.$signature";
    }

    public static function verifyToken($token) {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        [$header, $payloadEncoded, $signature] = $parts;
        $expectedSignature = hash_hmac('sha256', "$header.$payloadEncoded", self::$secret);

        if ($signature !== $expectedSignature) {
            return false;
        }

        $payload = json_decode(base64_decode($payloadEncoded), true);

        if (!$payload || !isset($payload['exp']) || $payload['exp'] < time()) {
            return false;
        }

        return $payload['user'] ?? false;
    }

    public static function requireAuth() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        if (!preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)) {
            http_response_code(401);
            echo json_encode(['error' => 'Token requerido']);
            exit;
        }

        $user = self::verifyToken($matches[1]);
        if (!$user) {
            http_response_code(403);
            echo json_encode(['error' => 'Token inválido']);
            exit;
        }

        return $user;
    }

    public static function checkRole($requiredRole) {
        $user = self::requireAuth();
        
        if ($requiredRole === 'SuperAdministrador' && $user['role'] !== 'SuperAdministrador') {
            http_response_code(403);
            echo json_encode(['error' => 'Permiso insuficiente']);
            exit;
        }

        return $user;
    }
}