<?php

require_once __DIR__ . '/mailer.php';
require_once dirname(__DIR__) . '/config/shop.php';



function adminEmail(): string
{
    return ADMIN_EMAIL;
}

function emailSubject(string $title): string
{
    return '[' . shopName() . '] ' . $title;
}

/**
 * Admin i përgjigjet klientit (kontakt ose email i drejtpërdrejtë).
 */
function sendEmailToCustomer(string $toEmail, string $customerName, string $subject, string $body): bool
{
    $message = "Përshëndetje " . $customerName . ",\n\n" . $body . "\n\n";
    $message .= "— Ekipi i " . shopName() . "\n";
    $message .= adminEmail();

    return sendAppEmail($toEmail, emailSubject($subject), $message, adminEmail());
}

/**
 * Njoftim për admin: mesazh i ri kontakti.
 */
function notifyAdminNewContact(string $fromName, string $fromEmail, string $subject, string $body, ?string $phone): bool
{
    $text = "Mesazh i ri nga faqja e kontaktit.\n\n";
    $text .= "Emri: $fromName\nEmail: $fromEmail\n";
    if ($phone) {
        $text .= "Tel: $phone\n";
    }
    $text .= "\n$body\n\nPërgjigjuni nga paneli: Mesazhet e klientëve.";

    return sendAppEmail(adminEmail(), emailSubject('Mesazh i ri: ' . $subject), $text, $fromEmail);
}

/**
 * Konfirmim për klientin pas dërgimit të kontaktit.
 */
function notifyCustomerContactReceived(string $email, string $name, string $subject): bool
{
    $body = "Kemi marrë mesazhin tuaj me temën \"$subject\".\n";
    $body .= "Do t'ju përgjigjemi sa më shpejt të jetë e mundur me email.";

    return sendEmailToCustomer($email, $name, 'Mesazhi u pranua', $body);
}
