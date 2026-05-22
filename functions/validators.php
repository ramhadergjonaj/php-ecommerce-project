<?php

function validateEmail(string $email): bool
{
    $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

    return preg_match($pattern, $email) === 1;
}

function validatePhoneNumber(string $phone): bool
{
    $cleanPhone = preg_replace('/[^0-9+]/', '', $phone);
    $pattern = '/^(\+\d{1,3})?\d{9,10}$/';

    return preg_match($pattern, $cleanPhone) === 1;
}

function validatePrice(string $price): bool
{
    return preg_match('/^\d+(\.\d{1,2})?$/', $price) === 1 && (float) $price > 0;
}

function validateProductName(string $name): bool
{
    return preg_match('/^[a-zA-Z0-9\s\-\.\']{3,200}$/u', $name) === 1;
}

function validatePassword(string $password): bool
{
    return strlen($password) >= 6 && strlen($password) <= 72;
}

function validateResetCode(string $code): bool
{
    return preg_match('/^\d{6}$/', preg_replace('/\D/', '', $code)) === 1;
}
