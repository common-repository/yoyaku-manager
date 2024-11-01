<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Services\Notification;

use Yoyaku\Domain\Setting\SettingsService;

/**
 * wp_mail()を使ってメールを送信するクラス
 */
class WpEmailService extends AEmailService
{
    /**
     * @param string $to
     * @param string $subject
     * @param string $body
     * @param array $attachments
     * @return bool Whether the email was sent successfully.
     */

    public function send($to, $subject, $body, $attachments = [])
    {
        $settings = SettingsService::get_instance();
        $headers = ['Content-Type: text/plain; charset=UTF-8'];

        $from_email = $settings->get('sender_email');
        $from_name = $settings->get('sender_name');
        // Fromヘッダー省略時は、名前が'WordPress'、メールアドレスが'wordpress@サイト名'になる
        if ($from_email) {
            $headers[] = "From: $from_name <$from_email>";
        }

        foreach ($settings->get_bcc_emails() as $email) {
            $headers[] = 'Bcc: ' . $email;
        }

        $attachments_locations = [];
        return wp_mail($to, $subject, $body, $headers, $attachments_locations);
    }
}
