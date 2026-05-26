<?php

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/functions/google_auth.php';

if (isLoggedIn()) {
    redirect_to('index.php');
}

$url = googleAuthUrl();

if (!$url) {
    $_SESSION['flash_error'] = 'Google Login nuk është konfiguruar. Kopjoni config/google.local.example.php → google.local.php';
    redirect_to('login.php');
}


header('Location: ' . $url);
exit; 
//.