<?php

/**
 * Dërgim email me SMTP (config/smtp.local.php) ose mail() si rezervë.
 */
function getSmtpConfig(): array
{
    static $config = null;

    if ($config === null) {
        $defaults = [
            'enabled' => false,
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'secure' => 'tls',
            'user' => '',
            'pass' => '',
            'from_email' => 'admin@shop.com',
            'from_name' => 'PHP Shop',
        ];

        if (file_exists(dirname(__DIR__) . '/config/smtp.local.php')) {
            require dirname(__DIR__) . '/config/smtp.local.php';
            $defaults['enabled'] = defined('SMTP_ENABLED') && SMTP_ENABLED;
            $defaults['host'] = SMTP_HOST ?? $defaults['host'];
            $defaults['port'] = (int) (SMTP_PORT ?? $defaults['port']);
            $defaults['secure'] = SMTP_SECURE ?? 'tls';
            $defaults['user'] = SMTP_USER ?? '';
            $defaults['pass'] = SMTP_PASS ?? '';
            $defaults['from_email'] = SMTP_FROM_EMAIL ?? $defaults['from_email'];
            $defaults['from_name'] = SMTP_FROM_NAME ?? $defaults['from_name'];
        }

        $config = $defaults;
    }

    return $config;
}

function sendAppEmail(string $to, string $subject, string $body, ?string $replyTo = null): bool
{
    $cfg = getSmtpConfig();

    if ($cfg['enabled'] && $cfg['user'] !== '' && $cfg['pass'] !== '') {
        try {
            return smtpSend($cfg, $to, $subject, $body, $replyTo);
        } catch (Throwable $e) {
            error_log('SMTP error: ' . $e->getMessage());
        }
    }

    $from = $cfg['from_email'];
    $headers = "From: {$cfg['from_name']} <{$from}>\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    if ($replyTo) {
        $headers .= 'Reply-To: ' . $replyTo . "\r\n";
    }

    return @mail($to, $subject, $body, $headers);
}

/**
 * @param array{enabled:bool,host:string,port:int,secure:string,user:string,pass:string,from_email:string,from_name:string} $cfg
 */
function smtpSend(array $cfg, string $to, string $subject, string $body, ?string $replyTo = null): bool
{
    $host = $cfg['host'];
    $port = $cfg['port'];
    $secure = strtolower($cfg['secure']);
    $remote = ($secure === 'ssl' ? 'ssl://' : '') . $host . ':' . $port;

    $socket = @stream_socket_client($remote, $errno, $errstr, 15);

    if (!$socket) {
        throw new RuntimeException("SMTP lidhja dështoi: $errstr ($errno)");
    }

    stream_set_timeout($socket, 15);
    smtpExpect($socket, [220]);
    smtpCmd($socket, 'EHLO localhost');
    smtpExpect($socket, [250]);

    if ($secure === 'tls') {
        smtpCmd($socket, 'STARTTLS');
        smtpExpect($socket, [220]);

        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            throw new RuntimeException('STARTTLS dështoi.');
        }

        smtpCmd($socket, 'EHLO localhost');
        smtpExpect($socket, [250]);
    }

    smtpCmd($socket, 'AUTH LOGIN');
    smtpExpect($socket, [334]);
    smtpCmd($socket, base64_encode($cfg['user']));
    smtpExpect($socket, [334]);
    smtpCmd($socket, base64_encode($cfg['pass']));
    smtpExpect($socket, [235]);

    $from = $cfg['from_email'];
    smtpCmd($socket, 'MAIL FROM:<' . $from . '>');
    smtpExpect($socket, [250]);
    smtpCmd($socket, 'RCPT TO:<' . $to . '>');
    smtpExpect($socket, [250, 251]);
    smtpCmd($socket, 'DATA');
    smtpExpect($socket, [354]);

    $message = 'From: ' . $cfg['from_name'] . ' <' . $from . ">\r\n";
    $message .= 'To: <' . $to . ">\r\n";

    if ($replyTo) {
        $message .= 'Reply-To: <' . $replyTo . ">\r\n";
    }

    $message .= 'Subject: ' . smtpEncodeSubject($subject) . "\r\n";
    $message .= "MIME-Version: 1.0\r\n";
    $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $message .= "\r\n" . $body . "\r\n.\r\n";

    fwrite($socket, $message);
    smtpExpect($socket, [250]);
    smtpCmd($socket, 'QUIT');
    fclose($socket);

    return true;
}

function smtpCmd($socket, string $cmd): void
{
    fwrite($socket, $cmd . "\r\n");
}

function smtpExpect($socket, array $codes): void
{
    $response = '';

    while ($line = fgets($socket, 515)) {
        $response .= $line;
        if (isset($line[3]) && $line[3] === ' ') {
            break;
        }
    }

    $code = (int) substr($response, 0, 3);

    if (!in_array($code, $codes, true)) {
        throw new RuntimeException('SMTP papritur: ' . trim($response));
    }
}

function smtpEncodeSubject(string $subject): string
{
    return '=?UTF-8?B?' . base64_encode($subject) . '?=';
}
