<?php declare(strict_types=1);

namespace Yoyaku\Application\EmailLog;

use PHPMailer\PHPMailer\Exception;
use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Domain\Notification\EmailLog;
use Yoyaku\Infrastructure\Repository\Notification\EmailLogRepository;
use Yoyaku\Infrastructure\Services\Notification\MailerFactory;

class EmailLogApplicationService
{
    protected EmailLogRepository $email_log_repo;

    /**
     * @param EmailLogRepository $email_log_repo
     */
    public function __construct(EmailLogRepository $email_log_repo)
    {
        $this->email_log_repo = $email_log_repo;
    }

    /**
     * 送信失敗した通知を再送する
     * @param array $filter
     * @return void
     * @throws WpDbException|Exception
     */
    public function send_undelivered_notifications($filter)
    {
        $mailer = MailerFactory::create();
        $email_logs = $this->email_log_repo->get_undelivered_notifications($filter);
        /** @var EmailLog $log */
        foreach ($email_logs->get_items() as $log) {
            $id = $log->get_id()->get_value();
            $is_sent = $mailer->send(
                $log->get_to()->get_value(),
                $log->get_subject()->get_value(),
                $log->get_content()->get_value(),
            );
            $this->email_log_repo->update(['sent' => $is_sent], ['id' => $id]);
        }
    }
}
