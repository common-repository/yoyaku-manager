<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Services\Notification;

abstract class AEmailService
{
    /**
     * @param      $to
     * @param      $subject
     * @param      $body
     * @param array $attachments
     * @return mixed
     */
    abstract public function send($to, $subject, $body, $attachments = []);
}
