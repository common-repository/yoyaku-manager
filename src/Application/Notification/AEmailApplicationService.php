<?php declare(strict_types=1);

namespace Yoyaku\Application\Notification;

use PHPMailer\PHPMailer\Exception;
use Yoyaku\Application\Common\Exceptions\NotAllowedError;
use Yoyaku\Application\EventType\Event\EventApplicationService;
use Yoyaku\Application\Payment\PaymentApplicationService;
use Yoyaku\Application\Placeholder\PlaceholderApplicationService;
use Yoyaku\Domain\DateTime\DateTimeService;
use Yoyaku\Domain\EventType\EventBookingPlaceholdersData;
use Yoyaku\Domain\Notification\EmailLogFactory;
use Yoyaku\Domain\Notification\EmailLogService;
use Yoyaku\Domain\Notification\Notification;
use Yoyaku\Domain\Setting\SettingsService;
use Yoyaku\Infrastructure\Repository\Notification\EmailLogRepository;
use Yoyaku\Infrastructure\Services\Notification\MailerFactory;

abstract class AEmailApplicationService
{
    protected EventApplicationService $event_as;
    protected PaymentApplicationService $payment_as;
    protected PlaceholderApplicationService $placeholder_as;
    protected EmailLogService $email_log_ds;
    protected EmailLogRepository $email_log_repo;
    protected SettingsService $settings;

    /**
     * @param EventApplicationService $event_as
     * @param PaymentApplicationService $payment_as
     * @param PlaceholderApplicationService $placeholder_as
     * @param EmailLogService $email_log_ds
     * @param EmailLogRepository $email_log_repo
     */
    public function __construct(
        EventApplicationService       $event_as,
        PaymentApplicationService     $payment_as,
        PlaceholderApplicationService $placeholder_as,
        EmailLogService               $email_log_ds,
        EmailLogRepository            $email_log_repo,
    )
    {
        $this->event_as = $event_as;
        $this->payment_as = $payment_as;
        $this->placeholder_as = $placeholder_as;
        $this->email_log_ds = $email_log_ds;
        $this->email_log_repo = $email_log_repo;
        $this->settings = SettingsService::get_instance();
    }

    /**
     * @param Notification $notification
     * @param EventBookingPlaceholdersData $placeholder_data
     * @throws NotAllowedError|Exception
     */
    public function send_notification($notification, $placeholder_data)
    {
        if (!$this->settings->get('sender_email')) {
            throw new NotAllowedError('cannot send email. sender_email is not set');
        }

        $mailer = MailerFactory::create();
        $notification_subject = $notification->get_subject()->get_value();
        $notification_content = $notification->get_content()->get_value();
        $data = $placeholder_data->get_placeholders_data();
        $subject = $this->placeholder_as->apply_placeholders($notification_subject, $data);
        $body = $this->placeholder_as->apply_placeholders($notification_content, $data);

        $booking = $placeholder_data->get_booking();
        $is_sent = $mailer->send(
            $booking->get_email()->get_value(),
            $subject,
            $body,
        );

        return EmailLogFactory::create([
            'customer_id' => $booking->get_customer_id()->get_value(),
            'sent' => $is_sent,
            'to' => $booking->get_email()->get_value(),
            'subject' => $subject,
            'content' => $body,
            'sent_datetime' => DateTimeService::get_now_datetime_in_utc(),
        ]);
    }
}
