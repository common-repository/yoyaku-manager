<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\WP\WPMenu\SubmenuPage;

class EmailLogs extends ASubmenuPage
{
    public function __construct()
    {
        parent::__construct(
            'EmailLogs',
            __('Email Logs', 'yoyaku-manager'),
            'yoyaku_read_emaillogs',
            'yoyaku-emaillogs',
        );
    }
}
