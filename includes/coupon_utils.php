<?php

function hh_normalize_coupon_code(string $code): string
{
    $code = trim($code);
    $code = preg_replace('/\s+/', '', $code);
    return strtoupper($code);
}

function hh_get_coupon_by_code(mysqli $conn, string $code): ?array
{
    $stmt = $conn->prepare("SELECT id, code, discount_percent, active, expires_at FROM coupons WHERE code = ? LIMIT 1");
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('s', $code);
    $stmt->execute();
    $result = $stmt->get_result();
    $coupon = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return $coupon ?: null;
}

function hh_is_coupon_valid(array $coupon): bool
{
    if (!isset($coupon['active']) || (int)$coupon['active'] !== 1) {
        return false;
    }

    if (!empty($coupon['expires_at'])) {
        $expires = strtotime($coupon['expires_at']);
        if ($expires !== false && $expires < time()) {
            return false;
        }
    }

    $percent = (float)($coupon['discount_percent'] ?? 0);
    if ($percent <= 0 || $percent > 100) {
        return false;
    }

    return true;
}

function hh_calculate_discount_amount(float $subtotal, float $discount_percent): float
{
    if ($subtotal <= 0 || $discount_percent <= 0) {
        return 0.0;
    }

    $amount = $subtotal * ($discount_percent / 100.0);
    $amount = round($amount, 2);

    if ($amount < 0) {
        return 0.0;
    }

    if ($amount > $subtotal) {
        return $subtotal;
    }

    return $amount;
}
