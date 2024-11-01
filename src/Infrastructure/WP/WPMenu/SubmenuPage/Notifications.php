<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\WP\WPMenu\SubmenuPage;

class Notifications extends ASubmenuPage
{
    public function __construct()
    {
        parent::__construct(
            'Notifications',
            __('Notifications', 'yoyaku-manager'),
            'yoyaku_read_notifications',
            'yoyaku-notifications',
        );
    }
}
