<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * JWT Helper
 * Lightweight JSON Web Token implementation for CodeIgniter 3.
 */

/**
 * Generate a JWT token.
 *
 * @param array  $payload  Data to encode (e.g. user id, role)
 * @param string $secret   Secret signing key
 * @param int    $expire   Token lifetime in seconds
 * @return string
 */
function jwt_encode(array $payload, string $secret, int $expire = 86400): string
{
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

    $payload['iat'] = time();
    $payload['exp'] = time() + $expire;

    $base64Header  = jwt_base64url_encode($header);
    $base64Payload = jwt_base64url_encode(json_encode($payload));
    $signature     = hash_hmac('sha256', $base64Header . '.' . $base64Payload, $secret, true);
    $base64Sig     = jwt_base64url_encode($signature);

    return $base64Header . '.' . $base64Payload . '.' . $base64Sig;
}

/**
 * Decode and verify a JWT token.
 *
 * @param string $token   JWT string
 * @param string $secret  Secret signing key
 * @return array|false    Decoded payload array on success, false on failure
 */
function jwt_decode(string $token, string $secret)
{
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return false;
    }

    [$b64Header, $b64Payload, $b64Signature] = $parts;

    $expectedSig = jwt_base64url_encode(
        hash_hmac('sha256', $b64Header . '.' . $b64Payload, $secret, true)
    );

    if (!hash_equals($expectedSig, $b64Signature)) {
        return false;
    }

    $payload = json_decode(jwt_base64url_decode($b64Payload), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return false;
    }

    if (isset($payload['exp']) && $payload['exp'] < time()) {
        return false; // Token expired
    }

    return $payload;
}

/**
 * Extract a Bearer token from the Authorization header.
 *
 * @return string|false
 */
function jwt_get_bearer_token()
{
    $headers = function_exists('apache_request_headers')
        ? apache_request_headers()
        : [];

    $authorization = $headers['Authorization']
        ?? $headers['authorization']
        ?? ($_SERVER['HTTP_AUTHORIZATION'] ?? '');

    if (empty($authorization) && function_exists('getallheaders')) {
        $all = getallheaders();
        $authorization = $all['Authorization'] ?? $all['authorization'] ?? '';
    }

    if (preg_match('/^Bearer\s+(.+)$/i', trim($authorization), $matches)) {
        return $matches[1];
    }

    return false;
}

// -----------------------------------------------------------------------

function jwt_base64url_encode(string $data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function jwt_base64url_decode(string $data): string
{
    return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
}
