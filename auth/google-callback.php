<?php

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/functions/helpers.php';
require_once dirname(__DIR__) . '/functions/google_auth.php';

if (isLoggedIn()) {
    redirect_to('index.php');
}

$code = $_GET['code'] ?? '';

if ($code === '') {
    $_SESSION['flash_error'] = 'Google auth u anulua.';
    redirect_to('login.php');
}

try {
    $tokenData = googleExchangeCode($code);

    if (empty($tokenData['access_token'])) {
        throw new RuntimeException('Token i pavlefshëm');
    }

    $profile = googleFetchUserInfo($tokenData['access_token']);

    if (!$profile || empty($profile['email'])) {
        throw new RuntimeException('Profili Google i paplotë');
    }
    //.
    $googleId = $profile['id'];
    $email = strtolower($profile['email']);
    $name = $profile['name'] ?? explode('@', $email)[0];

    $userRepo = app_repositories()['users'];
    $user = $userRepo->findByGoogleId($googleId);

    if (!$user) {
        $user = $userRepo->findByEmail($email);

        if ($user) {
            $userRepo->linkGoogleId($user->getId(), $googleId);
            $user = $userRepo->findById($user->getId());
        } else {
            $newId = $userRepo->createFromGoogle($googleId, $name, $email);
            $user = $userRepo->findById($newId);
            sendWelcomeEmail($email, $name);
        }
    }

    if (!$user) {
        throw new RuntimeException('Nuk u krijua përdoruesi');
    }

    loginUserSession($user);

    if ($user->getRole() === 'admin') {
        redirect_to('pages/dashboard.php');
    }

    redirect_to('index.php');
} catch (Throwable $e) {
    error_log('Google login: ' . $e->getMessage());
    $_SESSION['flash_error'] = 'Hyrja me Google nuk u përfundua. Provoni përsëri ose përdorni email/fjalëkalim.';
    redirect_to('login.php');
}