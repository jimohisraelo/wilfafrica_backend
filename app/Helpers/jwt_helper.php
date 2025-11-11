<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function getUserFromToken()
{
    $request = service('request');
    $authHeader = $request->getHeaderLine('Authorization');

    if (!$authHeader) {
        log_message('error', 'Authorization header missing.');
        return null;
    }

    if (strpos($authHeader, 'Bearer ') !== 0) {
        log_message('error', 'Authorization header format invalid: ' . $authHeader);
        return null;
    }

    $token = trim(str_replace('Bearer', '', $authHeader));
    $secret = getenv('JWT_SECRET') ?: 'your_jwt_secret_key';

    try {
        $decoded = JWT::decode($token, new Key($secret, 'HS256'));
        return $decoded->uid ?? null;
    } catch (\Exception $e) {
        log_message('error', 'JWT decode failed: ' . $e->getMessage());
        return null;
    }
}
