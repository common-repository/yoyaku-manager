<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Services\Notification;

class MailerFactory
{
    /**
     * @return WpEmailService
     */
    public static function create()
    {
        return new WpEmailService();
    }
}
