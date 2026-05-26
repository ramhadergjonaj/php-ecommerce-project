<?php

function getGoogleConfig(): ?array
{
    static $cfg = null;

    if ($cfg === null) {
        $file = dirname(__DIR__) . '/config/google.local.php';

        if (!file_exists($file)) {
            return null;
        }

        require $file;
        $cfg = [
            'client_id' => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri' => base_url('auth/google-callback.php'),
        ];
    }

    return $cfg;
}

function googleAuthUrl(): ?string
{
    $cfg = getGoogleConfig();

    if (!$cfg) {
        return null;
    }

    $params = http_build_query([
        'client_id' => $cfg['client_id'],
        'redirect_uri' => $cfg['redirect_uri'],
        'response_type' => 'code',
        'scope' => 'openid email profile',
        'access_type' => 'online',
        'prompt' => 'select_account',
    ]);

    return 'https://accounts.google.com/o/oauth2/v2/auth?' . $params;
}

function googleExchangeCode(string $code): ?array
{
    $cfg = getGoogleConfig();

    if (!$cfg) {
        return null;
    }

    $post = http_build_query([
        'code' => $code,
        'client_id' => $cfg['client_id'],
        'client_secret' => $cfg['client_secret'],
        'redirect_uri' => $cfg['redirect_uri'],
        'grant_type' => 'authorization_code',
    ]);

    $ctx = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => $post,
            'timeout' => 15,
        ],
    ]);

    $json = @file_get_contents('https://oauth2.googleapis.com/token', false, $ctx);

    if ($json === false) {
        return null;
    }

    $data = json_decode($json, true);

    return is_array($data) ? $data : null;
}
//.
function googleFetchUserInfo(string $accessToken): ?array
{
    $ctx = stream_context_create([
        'http' => [
            'header' => 'Authorization: Bearer ' . $accessToken . "\r\n",
            'timeout' => 15,
        ],
    ]);

    $json = @file_get_contents('https://www.googleapis.com/oauth2/v2/userinfo', false, $ctx);

    if ($json === false) {
        return null;
    }

    $data = json_decode($json, true);

    return is_array($data) && !empty($data['id']) ? $data : null;
}

function loginUserSession(User $user): void
{
    $_SESSION['user_id'] = $user->getId();
    $_SESSION['user_name'] = $user->getName();
    $_SESSION['user_email'] = $user->getEmail();
    $_SESSION['user_role'] = $user->getRole();
    setcookie('last_login', date('Y-m-d H:i:s'), time() + (86400 * 30), base_url() ?: '/');
}

function sendWelcomeEmail(string $email, string $name): void
{
    require_once __DIR__ . '/messaging.php';
    sendEmailToCustomer($email, $name, 'Mirësevini', 'Faleminderit që u bashkuat me ne. Llogaria juaj është aktive — mund të shfletoni produktet dhe të porosisni menjëherë.');
}