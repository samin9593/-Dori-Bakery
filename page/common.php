<?php

const DELIVERY_FEE = 7;
const MAX_CART_QUANTITY = 99;
const DISCOUNT_CODE = 'DORI10';

function order_discount(int $subtotal): float
{
    return empty($_SESSION['discount_applied']) ? 0.0 : ($subtotal + DELIVERY_FEE) / 10;
}

function money($amount): string
{
    return number_format((float) $amount, 2, '.', '');
}

ini_set('display_errors', '0');
ini_set('log_errors', '1');
session_name('DoriBakerySession');
session_start([
    'use_strict_mode' => true,
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'cookie_secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);

function escape($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function post_text(string $key): string
{
    $value = $_POST[$key] ?? null;
    return is_string($value) && mb_check_encoding($value, 'UTF-8') ? trim($value) : '';
}

function positive_id($value): int
{
    if (!is_string($value)) {
        return 0;
    }
    return filter_var($value, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 2147483647],
    ]) ?: 0;
}

function csrf_token(): string
{
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . escape(csrf_token()) . '">';
}

function require_post(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Allow: POST');
        http_response_code(405);
        exit('Use POST for this action.');
    }

    $token = post_text('csrf');
    if (!isset($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
        http_response_code(403);
        exit('The form expired. Reload the page and try again.');
    }
}

function require_login(): string
{
    if (!isset($_SESSION['user_email'])) {
        header('Location: login.php');
        exit;
    }
    return $_SESSION['user_email'];
}

function sign_in(string $email, string $name): void
{
    session_regenerate_id(true);
    $_SESSION = ['user_email' => $email, 'user_name' => $name];
}

function valid_phone(string $phone, int $maxLength): bool
{
    return strlen($phone) <= $maxLength
        && preg_match('/\A[0-9+(). -]+\z/', $phone)
        && preg_match('/[0-9]/', $phone);
}

function valid_password(string $password): bool
{
    return strlen($password) >= 8 && strlen($password) <= 72 && !str_contains($password, "\0");
}
