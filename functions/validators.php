<?php

function validateEmail($email) {
    $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
    return preg_match($pattern, $email) === 1;
}

function validatePhoneNumber($phone) {
    $cleanPhone = preg_replace('/[^0-9+]/', '', $phone);
    $pattern = '/^(\+\d{1,3})?\d{9,10}$/';
    return preg_match($pattern, $cleanPhone) === 1;
}

function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}